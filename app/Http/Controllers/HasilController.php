<?php

namespace App\Http\Controllers;

use App\Models\Aktifitas;
use App\Models\Countexamtime;
use App\Models\Jawab;
use App\Models\Kelas;
use App\Models\School;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\Detailsoal;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HasilController extends Controller {
    /**
     * Daftar paket yang sudah memiliki hasil.
     *
     * Admin:
     * - dapat melihat seluruh paket.
     *
     * Guru:
     * - hanya paket miliknya sendiri.
     */
    public function index(): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        $jawabs = $this->resultsQuery()->paginate(10);

        return view('guru.hasil', compact('user', 'school', 'jawabs'));
    }

    /**
     * Search laporan melalui AJAX.
     */
    public function search(Request $request): View {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
        ]);

        $q = trim((string) ($validated['q'] ?? ''));

        $jawabs = $this->resultsQuery($q)->limit(50)->get();

        return view('guru.ajax.get_hasil_guru', compact('jawabs'));
    }

    public function classDetail(int $id, int $idSoal): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        $soal = $this->findAccessiblePackage($idSoal);

        $kelas = Kelas::findOrFail($id);

        $jawabs = Jawab::query()
            ->join('users', 'jawabs.id_user', '=', 'users.id')
            ->select(
                'jawabs.id_user',
                'jawabs.id_kelas',
                'jawabs.id_soal',
                'users.nama',
                'users.no_induk',

                DB::raw(
                    "
                    SUM(
                        CAST(
                            COALESCE(
                                NULLIF(jawabs.score, ''),
                                '0'
                            )
                            AS DECIMAL(10,2)
                        )
                    ) as total_score
                    ",
                ),

                DB::raw('MAX(jawabs.updated_at) as selesai_pada'),
            )
            ->where('jawabs.id_kelas', (string) $kelas->id)
            ->where('jawabs.id_soal', (string) $soal->id)
            ->where('jawabs.status', 'Y')
            ->groupBy('jawabs.id_user', 'jawabs.id_kelas', 'jawabs.id_soal', 'users.nama', 'users.no_induk')
            ->orderBy('users.nama')
            ->get();

        $participantIds = $jawabs->pluck('id_user')->map(fn($id) => (string) $id)->all();

        $detailJawabs = collect();

        if ($participantIds !== []) {
            $detailJawabs = Jawab::query()
                ->join('detailsoals', 'jawabs.no_soal_id', '=', 'detailsoals.id')
                ->select(
                    'jawabs.id',
                    'jawabs.id_user',
                    'jawabs.id_kelas',
                    'jawabs.id_soal',
                    'jawabs.no_soal_id',
                    'jawabs.pilihan',
                    'jawabs.score',
                    'detailsoals.soal',
                    'detailsoals.kunci',
                    'detailsoals.score as max_score',
                )
                ->where('jawabs.id_kelas', (string) $kelas->id)
                ->where('jawabs.id_soal', (string) $soal->id)
                ->where('jawabs.status', 'Y')
                ->whereIn('jawabs.id_user', $participantIds)
                ->orderBy('jawabs.id_user')
                ->orderBy('jawabs.no_soal_id')
                ->get();
        }

        $answersByUser = $detailJawabs->groupBy('id_user');

        return view('guru.detailhasilsoal', compact('user', 'school', 'soal', 'kelas', 'jawabs', 'answersByUser'));
    }

    public function destroyStudentResult(Request $request): JsonResponse {
        $validated = $request->validate([
            'id_kelas' => ['required', 'integer'],

            'id_soal' => ['required', 'integer'],

            'id_user' => ['required', 'integer'],
        ]);

        /*
         * Pastikan paket dapat diakses
         * oleh Guru/Admin yang sedang login.
         */
        $soal = $this->findAccessiblePackage((int) $validated['id_soal']);

        /*
         * Kelas yang digunakan adalah kelas
         * historis ketika ujian berlangsung.
         */
        $kelas = Kelas::findOrFail((int) $validated['id_kelas']);

        /*
         * Sumber kebenaran histori adalah jawabs,
         * BUKAN users.id_kelas saat ini.
         *
         * Dengan demikian siswa yang sudah pindah
         * kelas tetap dapat dikelola hasil lamanya.
         */
        $historicalAnswer = Jawab::query()
            ->where('id_soal', (string) $soal->id)
            ->where('id_kelas', (string) $kelas->id)
            ->where('id_user', (string) $validated['id_user'])
            ->first();

        if (!$historicalAnswer) {
            return response()->json(
                [
                    'message' => 'Hasil historis siswa tidak ditemukan.',
                ],
                404,
            );
        }

        /*
         * Data user hanya digunakan untuk
         * mendapatkan nama terbaru.
         *
         * Tidak ada validasi terhadap
         * users.id_kelas karena siswa mungkin
         * sudah berpindah kelas.
         */
        $student = User::find((int) $validated['id_user']);

        /*
         * jawabs.nama menyimpan snapshot nama
         * ketika siswa mengerjakan ujian.
         *
         * Digunakan sebagai fallback apabila
         * akun user sudah tidak tersedia.
         */
        $studentName = $student?->nama ?? ($historicalAnswer->nama ?? 'User ID ' . $validated['id_user']);

        $deleted = DB::transaction(function () use ($soal, $kelas, $validated) {
            /*
             * Hapus seluruh jawaban milik siswa
             * pada paket + kelas historis tersebut.
             */
            $deleted = Jawab::query()
                ->where('id_soal', (string) $soal->id)
                ->where('id_kelas', (string) $kelas->id)
                ->where('id_user', (string) $validated['id_user'])
                ->delete();

            /*
             * countexamtimes tidak memiliki
             * kolom id_kelas pada schema legacy.
             *
             * Karena timer terikat ke kombinasi
             * paket + siswa, hapus berdasarkan
             * dua field tersebut.
             */
            Countexamtime::query()
                ->where('id_soal', (string) $soal->id)
                ->where('id_user', (string) $validated['id_user'])
                ->delete();

            return $deleted;
        });

        Aktifitas::create([
            'id_user' => auth()->id(),

            'nama' =>
                'Menghapus hasil ' .
                strtolower($this->assessmentLabel($soal)) .
                ' siswa ' .
                $studentName .
                ' pada kelas ' .
                $kelas->nama .
                ' paket ' .
                $soal->paket .
                '.',
        ]);

        return response()->json([
            'success' => true,

            'deleted' => $deleted,

            'message' => 'Hasil siswa berhasil dihapus.',
        ]);
    }

    public function destroyClassResults(Request $request): JsonResponse {
        $validated = $request->validate([
            'id_kelas' => ['required', 'integer'],

            'id_soal' => ['required', 'integer'],
        ]);

        $soal = $this->findAccessiblePackage((int) $validated['id_soal']);

        $kelas = Kelas::findOrFail((int) $validated['id_kelas']);

        $deleted = DB::transaction(function () use ($soal, $kelas) {
            $studentIds = Jawab::query()
                ->where('id_soal', (string) $soal->id)
                ->where('id_kelas', (string) $kelas->id)
                ->pluck('id_user')
                ->map(fn($id) => (string) $id)
                ->unique()
                ->values()
                ->all();

            $deleted = Jawab::query()
                ->where('id_soal', (string) $soal->id)
                ->where('id_kelas', (string) $kelas->id)
                ->delete();

            if ($studentIds !== []) {
                Countexamtime::query()->where('id_soal', (string) $soal->id)->whereIn('id_user', $studentIds)->delete();
            }

            return $deleted;
        });

        Aktifitas::create([
            'id_user' => auth()->id(),

            'nama' =>
                'Menghapus seluruh hasil ' .
                strtolower($this->assessmentLabel($soal)) .
                ' kelas ' .
                $kelas->nama .
                ' pada paket ' .
                $soal->paket .
                '.',
        ]);

        return response()->json([
            'success' => true,

            'deleted' => $deleted,

            'message' => 'Hasil kelas berhasil dihapus.',
        ]);
    }

    public function detail(int $id): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        $soal = $this->findAccessiblePackage($id);

        $jawabs = Jawab::query()
            ->join('kelas', 'jawabs.id_kelas', '=', 'kelas.id')
            ->select(
                'kelas.id as id_kelas',
                'kelas.nama as nama_kelas',
                'jawabs.id_soal',

                DB::raw('COUNT(DISTINCT jawabs.id_user) as jumlah_peserta'),

                DB::raw('MAX(jawabs.updated_at) as terakhir_dikerjakan'),
            )
            ->where('jawabs.id_soal', (string) $soal->id)
            ->where('jawabs.status', 'Y')
            ->whereNotNull('jawabs.id_kelas')
            ->groupBy('kelas.id', 'kelas.nama', 'jawabs.id_soal')
            ->orderBy('kelas.nama')
            ->paginate(15);

        $aktifitas = $this->recentActivities();

        return view('guru.detailhasil', compact('user', 'school', 'soal', 'jawabs', 'aktifitas'));
    }

    /**
     * Query dasar laporan.
     *
     * Hanya jawaban final status=Y yang
     * dianggap sebagai hasil pengerjaan.
     */
    private function resultsQuery(?string $search = null): Builder {
        $query = Jawab::query()
            ->join('soals', 'jawabs.id_soal', '=', 'soals.id')
            ->select(
                'soals.id as id_soal',
                'soals.id_user',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.waktu',
                'soals.jenis',
                'soals.created_at',

                \DB::raw('COUNT(DISTINCT jawabs.id_user) as jumlah_peserta'),

                \DB::raw('MAX(jawabs.updated_at) as terakhir_dikerjakan'),
            )
            ->where('jawabs.status', 'Y');

        /*
         * Guru hanya boleh melihat
         * hasil paket yang dibuat olehnya.
         *
         * Admin dapat melihat semua.
         */
        if (auth()->user()->status === 'G') {
            $query->where('soals.id_user', (string) auth()->id());
        }

        if ($search !== null && $search !== '') {
            $query->where('soals.paket', 'like', '%' . $search . '%');
        }

        return $query
            ->groupBy(
                'soals.id',
                'soals.id_user',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.waktu',
                'soals.jenis',
                'soals.created_at',
            )
            ->orderByDesc('terakhir_dikerjakan');
    }

    private function classResultRows(int $idKelas, int $idSoal) {
        return Jawab::query()
            ->join('users', 'jawabs.id_user', '=', 'users.id')
            ->select(
                'jawabs.id_user',
                'users.no_induk',
                'users.nama',
                'users.sekolah_asal',

                DB::raw(
                    "
                SUM(
                    CASE
                        WHEN CAST(
                            COALESCE(
                                NULLIF(jawabs.score, ''),
                                '0'
                            )
                            AS DECIMAL(10,2)
                        ) <> 0
                        THEN 1
                        ELSE 0
                    END
                ) as jawaban_benar
                ",
                ),

                DB::raw(
                    "
                SUM(
                    CAST(
                        COALESCE(
                            NULLIF(jawabs.score, ''),
                            '0'
                        )
                        AS DECIMAL(10,2)
                    )
                ) as nilai
                ",
                ),
            )
            ->where('jawabs.id_kelas', (string) $idKelas)
            ->where('jawabs.id_soal', (string) $idSoal)
            ->where('jawabs.status', 'Y')
            ->groupBy('jawabs.id_user', 'users.no_induk', 'users.nama', 'users.sekolah_asal')
            ->orderBy('users.nama')
            ->get();
    }

    public function exportClassResults(int $id, int $idSoal): StreamedResponse {
        $soal = $this->findAccessiblePackage($idSoal);

        $kelas = Kelas::findOrFail($id);

        $results = $this->classResultRows($kelas->id, $soal->id);

        $jumlahSoal = Detailsoal::query()->where('id_soal', (string) $soal->id)->where('status', 'Y')->count();

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Rekap Nilai');

        /*
        |--------------------------------------------------------------------------
        | Informasi Paket
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue('A1', 'Paket Soal');

        $sheet->setCellValue('B1', $soal->paket);

        $sheet->setCellValue('A2', 'Kelas');

        $sheet->setCellValue('B2', $kelas->nama);

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        $headers = ['NIS', 'Nama', 'Jumlah Soal', 'Jawaban Benar', 'Nilai'];

        $sheet->fromArray($headers, null, 'A4');

        /*
        |--------------------------------------------------------------------------
        | Data
        |--------------------------------------------------------------------------
        */

        $row = 5;

        foreach ($results as $result) {
            $sheet->setCellValue('A' . $row, (string) $result->no_induk);

            $sheet->setCellValue('B' . $row, $result->nama);

            $sheet->setCellValue('C' . $row, $jumlahSoal);

            $sheet->setCellValue('D' . $row, (int) $result->jawaban_benar);

            $sheet->setCellValue('E' . $row, (float) $result->nilai);

            $row++;
        }

        /*
        |--------------------------------------------------------------------------
        | Formatting
        |--------------------------------------------------------------------------
        */

        $lastRow = max(5, $row - 1);

        $sheet->getStyle('A1:A2')->getFont()->setBold(true);

        $sheet->getStyle('A4:E4')->getFont()->setBold(true);

        $sheet
            ->getStyle('A4:E' . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet
            ->getStyle('A4:E' . $lastRow)
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet
            ->getStyle('A4:A' . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet
            ->getStyle('C4:E' . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(18);

        $sheet->getColumnDimension('B')->setWidth(32);

        $sheet->getColumnDimension('C')->setWidth(15);

        $sheet->getColumnDimension('D')->setWidth(18);

        $sheet->getColumnDimension('E')->setWidth(12);

        $sheet->freezePane('A5');

        /*
        |--------------------------------------------------------------------------
        | Download
        |--------------------------------------------------------------------------
        */

        $filename = Str::slug('rekap-' . $kelas->nama . '-' . $soal->paket) . '.xlsx';

        return response()->streamDownload(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);

                $writer->save('php://output');

                $spreadsheet->disconnectWorksheets();
            },
            $filename,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        );
    }

    private function findAccessiblePackage(int $id): Soal {
        return Soal::query()
            ->whereKey($id)
            ->when(
                auth()->user()->status === 'G',

                fn($query) => $query->where('id_user', (string) auth()->id()),
            )
            ->firstOrFail();
    }

    private function recentActivities() {
        return Aktifitas::query()
            ->join('users', 'aktifitas.id_user', '=', 'users.id')
            ->select(
                'users.nama as nama_user',
                'users.gambar',
                'aktifitas.id',
                'aktifitas.id_user',
                'aktifitas.nama',
                'aktifitas.created_at',
                'aktifitas.updated_at',
            )
            ->orderByDesc('aktifitas.id')
            ->limit(3)
            ->get();
    }

    public function displayClassResults(int $id, int $idSoal): View {
        $soal = $this->findAccessiblePackage($idSoal);

        $kelas = Kelas::findOrFail($id);

        $results = $this->classResultRows($kelas->id, $soal->id);

        return view('guru.tampil', compact('soal', 'kelas', 'results'));
    }

    private function assessmentLabel(Soal $soal): string {
        return (string) $soal->jenis === '2' ? 'Latihan' : 'Ujian';
    }
}
