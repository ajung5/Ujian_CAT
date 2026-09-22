<?php

namespace App\Http\Controllers;

use App\Models\Detailsoal;
use App\Models\Distribusisoal;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\School;
use App\Models\Soal;
use App\Models\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use Throwable;

class SoalController extends Controller {
    /**
     * Daftar Paket Soal.
     */
    public function index(): View {
        $user = User::findOrFail(auth()->id());

        $query = Soal::query();

        /*
         * Legacy:
         * Admin melihat seluruh paket soal.
         * Guru hanya melihat paket soal miliknya.
         */
        if (auth()->user()->status === 'G') {
            $query->where('id_user', (string) auth()->id());
        }

        $soals = $query->orderByDesc('id')->paginate(10);

        /*
         * Materi yang dapat dipilih untuk jenis Latihan.
         *
         * Materi harus milik user yang sedang login.
         * Materi yang sudah dipakai paket latihan
         * tidak ditawarkan kembali.
         */
        $materiTerpakai = Soal::query()->whereNotNull('materi')->where('materi', '!=', 0)->pluck('materi');

        $materis = Materi::query()
            ->where('id_user', auth()->id())
            ->whereNotIn('id', $materiTerpakai)
            ->orderBy('judul')
            ->get();

        return view('guru.soal', compact('user', 'soals', 'materis'));
    }

    /**
     * Search Paket Soal.
     */
    public function search(Request $request): View {
        $q = trim((string) $request->input('q', ''));

        $query = Soal::query();

        if (auth()->user()->status === 'G') {
            $query->where('id_user', (string) auth()->id());
        }

        if ($q !== '') {
            $query->where('paket', 'like', '%' . $q . '%');
        }

        $soals = $query->orderByDesc('id')->paginate(15);

        return view('guru.ajax.get_soal_guru', compact('soals', 'q'));
    }

    /**
     * Membuat Paket Soal.
     *
     * Legacy:
     * POST /simpanformsoal
     */
    public function store(Request $request): Response {
        $validated = $request->validate(
            [
                'jenis' => ['required', Rule::in(['1', '2'])],

                'materi' => ['nullable', 'integer', 'exists:materis,id'],

                'paket' => ['required', 'string', 'max:255'],

                'deskripsi' => ['required', 'string', 'max:255'],

                'kkm' => ['required', 'integer', 'min:0', 'max:99999'],

                'waktu' => ['required', 'integer', 'min:1'],
            ],
            [
                'jenis.required' => 'Anda belum memilih jenis soal.',

                'jenis.in' => 'Jenis soal tidak valid.',

                'materi.exists' => 'Materi yang dipilih tidak ditemukan.',

                'paket.required' => 'Anda belum menuliskan paket soal.',

                'deskripsi.required' => 'Anda belum menuliskan deskripsi soal.',

                'kkm.required' => 'Anda belum menuliskan KKM soal.',

                'kkm.integer' => 'KKM harus berupa bilangan bulat.',

                'waktu.required' => 'Anda belum menuliskan waktu soal.',

                'waktu.integer' => 'Waktu harus ditulis dalam detik.',
            ],
        );

        /*
         * Jenis 2 = Latihan.
         *
         * Legacy mewajibkan materi.
         */
        if ($validated['jenis'] === '2' && empty($validated['materi'])) {
            return response('<b>Error:</b> Anda belum memilih materi soal', 422);
        }

        /*
         * Materi harus dimiliki user yang login.
         */
        if ($validated['jenis'] === '2') {
            $materiValid = Materi::query()->whereKey($validated['materi'])->where('id_user', auth()->id())->exists();

            if (!$materiValid) {
                abort(403);
            }
        }

        $soal = new Soal();

        $soal->id_user = (string) auth()->id();

        $soal->jenis = $validated['jenis'];

        /*
         * Ujian tidak mempunyai relasi Materi.
         */
        $soal->materi = $validated['jenis'] === '2' ? $validated['materi'] : null;

        $soal->paket = trim($validated['paket']);

        $soal->deskripsi = trim($validated['deskripsi']);

        $soal->kkm = (string) $validated['kkm'];

        /*
         * Tetap disimpan dalam detik seperti legacy.
         */
        $soal->waktu = (string) $validated['waktu'];

        /*
         * Schema legacy memperbolehkan NULL.
         */
        $soal->tampil = null;

        $soal->save();

        return response('berhasil');
    }

    /**
     * Form edit Paket Soal.
     */
    public function edit(int $id): View {
        $user = User::findOrFail(auth()->id());

        $soal = $this->findAccessibleSoal($id);

        return view('guru.editsoal', compact('user', 'soal'));
    }

    /**
     * Update Paket Soal.
     *
     * Sesuai legacy, edit hanya:
     * - paket
     * - deskripsi
     * - KKM
     * - waktu
     *
     * jenis dan materi tidak diubah.
     */
    public function update(Request $request): Response {
        $validated = $request->validate(
            [
                'id_soal' => ['required', 'integer'],

                'paket' => ['required', 'string', 'max:255'],

                'deskripsi' => ['required', 'string', 'max:255'],

                'kkm' => ['required', 'integer', 'min:0', 'max:99999'],

                'waktu' => ['required', 'integer', 'min:1'],
            ],
            [
                'paket.required' => 'Anda belum menuliskan paket soal.',

                'deskripsi.required' => 'Anda belum menuliskan deskripsi soal.',

                'kkm.required' => 'Anda belum menuliskan KKM soal.',

                'waktu.required' => 'Anda belum menuliskan waktu soal.',
            ],
        );

        $soal = $this->findAccessibleSoal((int) $validated['id_soal']);

        $soal->paket = trim($validated['paket']);

        $soal->deskripsi = trim($validated['deskripsi']);

        $soal->kkm = (string) $validated['kkm'];

        $soal->waktu = (string) $validated['waktu'];

        $soal->save();

        return response('berhasil');
    }

    /**
     * Menghapus Paket Soal.
     *
     * Paket yang sudah pernah dikerjakan siswa
     * tidak boleh dihapus agar histori hasil ujian
     * tetap terjaga.
     *
     * Paket yang belum pernah dikerjakan akan
     * dibersihkan bersama detail soal, distribusi,
     * dan file audio terkait.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse {
        $soal = $this->findAccessibleSoal($id);

        /*
         * Jangan menghapus paket yang sudah
         * mempunyai histori pengerjaan.
         *
         * jawabs:
         * - draft jawaban
         * - hasil final
         *
         * countexamtimes:
         * - menandakan siswa pernah
         *   memulai assessment
         */
        $sudahDikerjakan = $soal->jawabs()->exists() || $soal->countexamtimes()->exists();

        if ($sudahDikerjakan) {
            $message = 'Paket soal tidak dapat dihapus ' . 'karena sudah memiliki riwayat ' . 'pengerjaan siswa.';

            if ($request->expectsJson()) {
                return response()->json(
                    [
                        'message' => $message,
                    ],
                    409,
                );
            }

            return redirect()
                ->route('guru.soal')
                ->withErrors([
                    'delete' => $message,
                ]);
        }

        /*
         * Simpan daftar file audio terlebih dahulu.
         *
         * File baru dihapus SETELAH transaksi
         * database berhasil, sehingga rollback
         * database tidak menyebabkan referensi
         * audio rusak.
         */
        $audioFiles = $soal
            ->detailsoals()
            ->whereNotNull('audio')
            ->pluck('audio')
            ->filter()
            ->map(fn($audio) => basename((string) $audio))
            ->unique()
            ->values()
            ->all();

        /*
         * Bersihkan dependent records secara
         * atomik.
         *
         * Jika salah satu query gagal,
         * seluruh operasi database di-rollback.
         */
        DB::transaction(function () use ($soal) {
            /*
             * Hapus distribusi paket
             * ke kelas.
             */
            $soal->distribusisoals()->delete();

            /*
             * Hapus seluruh pertanyaan
             * dalam paket.
             */
            $soal->detailsoals()->delete();

            /*
             * Terakhir hapus paket induk.
             */
            $soal->delete();
        });

        /*
         * Setelah transaksi database berhasil,
         * bersihkan file audio fisik.
         */
        foreach ($audioFiles as $audioFile) {
            $audioPath = public_path('assets/audios/' . $audioFile);

            if (File::exists($audioPath)) {
                File::delete($audioPath);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Paket soal berhasil dihapus.',
            ]);
        }

        return redirect()->route('guru.soal')->with('success', 'Paket soal berhasil dihapus.');
    }

    public function detail(int $id): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        $soal = $this->findAccessibleSoal($id);

        $detailsoals = Detailsoal::query()->where('id_soal', (string) $soal->id)->orderBy('id')->get();

        $kelas = Kelas::query()->orderBy('nama')->get();

        $kelasTerdistribusi = Distribusisoal::query()
            ->where('id_soal', (string) $soal->id)
            ->pluck('id_kelas')
            ->map(fn($idKelas) => (string) $idKelas)
            ->all();

        /*
         * Schema legacy:
         * detailsoals.sesi VARCHAR(32)
         */
        $sesiBaru = Str::random(32);

        return view(
            'guru.detailsoal',
            compact('user', 'school', 'soal', 'detailsoals', 'kelas', 'kelasTerdistribusi', 'sesiBaru'),
        );
    }

    public function storeDetail(Request $request): Response {
        $validated = $request->validate(
            [
                'paket' => ['required', 'integer'],

                'sesi' => ['required', 'string', 'size:32'],

                'soal' => ['required', 'string'],

                'pila' => ['required', 'string'],

                'pilb' => ['required', 'string'],

                'pilc' => ['required', 'string'],

                'pild' => ['required', 'string'],

                'pile' => ['required', 'string'],

                'kunci' => ['required', Rule::in(['A', 'B', 'C', 'D', 'E'])],

                'score' => ['required', 'string', 'max:50'],

                'status' => ['required', Rule::in(['Y', 'N'])],
            ],
            [
                'soal.required' => 'Soal belum diisi.',

                'pila.required' => 'Pilihan A belum diisi.',

                'pilb.required' => 'Pilihan B belum diisi.',

                'pilc.required' => 'Pilihan C belum diisi.',

                'pild.required' => 'Pilihan D belum diisi.',

                'pile.required' => 'Pilihan E belum diisi.',

                'kunci.required' => 'Kunci jawaban belum dipilih.',

                'score.required' => 'Score belum diisi.',

                'status.required' => 'Status belum dipilih.',

                'sesi.size' => 'Sesi detail soal tidak valid.',
            ],
        );

        /*
         * Pastikan paket soal dapat
         * diakses user login.
         */
        $paket = $this->findAccessibleSoal((int) $validated['paket']);

        /*
         * Audio mungkin sudah di-upload
         * terlebih dahulu.
         *
         * Jika demikian sudah terdapat
         * placeholder berdasarkan sesi.
         */
        $detail = Detailsoal::query()
            ->where('sesi', $validated['sesi'])
            ->where('id_user', (string) auth()->id())
            ->first();

        if (!$detail) {
            $detail = new Detailsoal();

            $detail->audio = null;

            $detail->sesi = $validated['sesi'];

            $detail->id_user = (string) auth()->id();
        }

        $detail->id_soal = (string) $paket->id;

        /*
         * Kolom legacy ini NOT NULL.
         * Data lama umumnya memakai ''.
         */
        $detail->jenis = '';

        $detail->soal = $validated['soal'];

        $detail->pila = $validated['pila'];

        $detail->pilb = $validated['pilb'];

        $detail->pilc = $validated['pilc'];

        $detail->pild = $validated['pild'];

        $detail->pile = $validated['pile'];

        $detail->kunci = $validated['kunci'];

        $detail->score = $validated['score'];

        $detail->status = $validated['status'];

        $detail->save();

        return response('berhasil');
    }

    public function editDetail(int $id): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        $detailsoal = $this->findAccessibleDetail($id);

        $soal = $this->findAccessibleSoal((int) $detailsoal->id_soal);

        return view('guru.ubahdetailsoal', compact('user', 'school', 'soal', 'detailsoal'));
    }

    public function updateDetail(Request $request): Response {
        $validated = $request->validate([
            'id_soal' => ['required', 'integer'],

            'soal' => ['required', 'string'],

            'pila' => ['required', 'string'],

            'pilb' => ['required', 'string'],

            'pilc' => ['required', 'string'],

            'pild' => ['required', 'string'],

            'pile' => ['required', 'string'],

            'kunci' => ['required', Rule::in(['A', 'B', 'C', 'D', 'E'])],

            'score' => ['required', 'string', 'max:50'],

            'status' => ['required', Rule::in(['Y', 'N'])],
        ]);

        $detail = $this->findAccessibleDetail((int) $validated['id_soal']);

        $detail->soal = $validated['soal'];

        $detail->pila = $validated['pila'];

        $detail->pilb = $validated['pilb'];

        $detail->pilc = $validated['pilc'];

        $detail->pild = $validated['pild'];

        $detail->pile = $validated['pile'];

        $detail->kunci = $validated['kunci'];

        $detail->score = $validated['score'];

        $detail->status = $validated['status'];

        $detail->save();

        return response('berhasil');
    }

    public function destroyDetail(Request $request): Response {
        $validated = $request->validate([
            'id_soal' => ['required', 'integer'],
        ]);

        $detail = $this->findAccessibleDetail((int) $validated['id_soal']);

        if (!empty($detail->audio)) {
            $audioPath = public_path('assets/audios/' . basename($detail->audio));

            if (File::exists($audioPath)) {
                File::delete($audioPath);
            }
        }

        $detail->delete();

        return response('Soal berhasil dihapus.');
    }

    #Audio
    public function uploadAudio(Request $request): Response {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],

            'tampil' => ['required', 'string'],
        ]);

        $file = $request->file('file');

        /*
         * Validasi extension secara eksplisit
         * untuk kompatibilitas file audio legacy.
         */
        $extension = strtolower($file->getClientOriginalExtension());

        $allowedExtensions = ['wav', 'wv', 'm4a', 'm4b', 'm4p', 'm4v', 'm4r', '3gp', 'mp4', 'aac', 'mp3', 'wma'];

        if (!in_array($extension, $allowedExtensions, true)) {
            return response('Maaf hanya file audio ' . 'WAV, WV, MP4, MP3, ' . 'WMA, M4A dan AAC.', 422);
        }

        $directory = public_path('assets/audios');

        File::ensureDirectoryExists($directory);

        $filename = now()->format('ymdHis') . Str::random(12) . '.' . $extension;

        /*
         * CREATE MODE
         *
         * tampil=N berarti user sedang
         * membuat detail soal baru.
         */
        if ($request->input('tampil') === 'N') {
            $request->validate([
                'sesi' => ['required', 'string', 'size:32'],
            ]);

            $sesi = (string) $request->input('sesi');

            $detail = Detailsoal::query()->where('sesi', $sesi)->where('id_user', (string) auth()->id())->first();

            if (!$detail) {
                /*
                 * Placeholder kompatibel
                 * dengan MySQL strict mode.
                 *
                 * Field-field NOT NULL harus
                 * diberi nilai.
                 */
                $detail = new Detailsoal();

                $detail->id_soal = '';

                $detail->jenis = '';

                $detail->soal = '';

                $detail->pila = '';

                $detail->pilb = '';

                $detail->pilc = '';

                $detail->pild = '';

                $detail->pile = '';

                $detail->kunci = '';

                $detail->score = null;

                $detail->id_user = (string) auth()->id();

                $detail->status = 'N';

                $detail->sesi = $sesi;
            }
        } else {
            /*
             * EDIT MODE
             *
             * tampil berisi ID detail soal.
             */
            $detail = $this->findAccessibleDetail((int) $request->input('tampil'));
        }

        /*
         * Upload file setelah
         * validasi berhasil.
         */
        $file->move($directory, $filename);

        $oldAudio = $detail->audio;

        $detail->audio = $filename;

        $detail->save();

        /*
         * Hapus audio lama setelah
         * DB berhasil di-update.
         */
        if (!empty($oldAudio)) {
            $oldPath = $directory . DIRECTORY_SEPARATOR . basename($oldAudio);

            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        return response('ok');
    }

    public function destroyAudio(Request $request): Response {
        $validated = $request->validate([
            'id_soal' => ['required', 'integer'],
        ]);

        $detail = $this->findAccessibleDetail((int) $validated['id_soal']);

        if (!empty($detail->audio)) {
            $path = public_path('assets/audios/' . basename($detail->audio));

            if (File::exists($path)) {
                File::delete($path);
            }

            $detail->audio = null;

            $detail->save();
        }

        return response('ok');
    }
    # end audio

    public function storeDistribution(Request $request): Response {
        $validated = $request->validate([
            'id_soal' => ['required', 'integer'],

            'id_kelas' => ['required', 'integer', 'exists:kelas,id'],
        ]);

        $soal = $this->findAccessibleSoal((int) $validated['id_soal']);

        /*
         * Distribusi kelas hanya
         * berlaku pada jenis Ujian.
         */
        if ((string) $soal->jenis !== '1') {
            abort(422);
        }

        Distribusisoal::query()->firstOrCreate([
            'id_soal' => (string) $soal->id,

            'id_kelas' => (string) $validated['id_kelas'],
        ]);

        return response('ok');
    }

    public function destroyDistribution(Request $request): Response {
        $validated = $request->validate([
            'id_soal' => ['required', 'integer'],

            'id_kelas' => ['required', 'integer'],
        ]);

        $soal = $this->findAccessibleSoal((int) $validated['id_soal']);

        Distribusisoal::query()
            ->where('id_soal', (string) $soal->id)
            ->where('id_kelas', (string) $validated['id_kelas'])
            ->delete();

        return response('ok');
    }

    public function importQuestions(Request $request): RedirectResponse {
        $validated = $request->validate(
            [
                'file' => ['required', 'file', 'extensions:xls,xlsx', 'max:10240'],
            ],
            [
                'file.required' => 'File Excel belum dipilih.',

                'file.extensions' => 'File harus menggunakan format XLS atau XLSX.',

                'file.max' => 'Ukuran file maksimal 10 MB.',
            ],
        );

        $file = $request->file('file');

        try {
            /*
             * PhpSpreadsheet otomatis mendeteksi
             * XLS lama maupun XLSX.
             */
            $reader = IOFactory::createReaderForFile($file->getRealPath());

            /*
             * Kita hanya membutuhkan data cell.
             */
            $reader->setReadDataOnly(true);

            $spreadsheet = $reader->load($file->getRealPath());

            $sheet = $spreadsheet->getActiveSheet();

            $highestRow = $sheet->getHighestDataRow();
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'import' => 'File Excel tidak dapat dibaca. ' . 'Pastikan file XLS/XLSX valid.',
            ]);
        }

        /*
         * Paket soal yang diperbolehkan.
         *
         * Admin:
         * seluruh paket.
         *
         * Guru:
         * hanya paket miliknya.
         */
        $paketQuery = Soal::query();

        if (auth()->user()->status === 'G') {
            $paketQuery->where('id_user', (string) auth()->id());
        }

        $paketValid = $paketQuery
            ->pluck('id')
            ->mapWithKeys(
                fn($id) => [
                    (string) $id => true,
                ],
            )
            ->all();

        $sukses = 0;
        $gagal = 0;
        $kosong = 0;

        $errors = [];

        /*
         * Baris 1 = header.
         * Data dimulai baris 2.
         */
        for ($row = 2; $row <= $highestRow; $row++) {
            $idSoal = $this->spreadsheetCell($sheet->getCell('A' . $row)->getValue());

            $soal = $this->spreadsheetCell($sheet->getCell('B' . $row)->getValue());

            $pila = $this->spreadsheetCell($sheet->getCell('C' . $row)->getValue());

            $pilb = $this->spreadsheetCell($sheet->getCell('D' . $row)->getValue());

            $pilc = $this->spreadsheetCell($sheet->getCell('E' . $row)->getValue());

            $pild = $this->spreadsheetCell($sheet->getCell('F' . $row)->getValue());

            $pile = $this->spreadsheetCell($sheet->getCell('G' . $row)->getValue());

            $kunci = strtoupper($this->spreadsheetCell($sheet->getCell('H' . $row)->getValue()));

            $score = $this->spreadsheetCell($sheet->getCell('I' . $row)->getValue());

            /*
             * Lewati baris kosong.
             */
            if (
                $idSoal === '' &&
                $soal === '' &&
                $pila === '' &&
                $pilb === '' &&
                $pilc === '' &&
                $pild === '' &&
                $pile === '' &&
                $kunci === '' &&
                $score === ''
            ) {
                $kosong++;

                continue;
            }

            $rowErrors = [];

            /*
             * Validasi paket.
             */
            if ($idSoal === '') {
                $rowErrors[] = 'ID Paket Soal kosong.';
            } elseif (!isset($paketValid[$idSoal])) {
                $rowErrors[] = 'ID Paket Soal ' . $idSoal . ' tidak ditemukan atau ' . 'tidak dapat diakses.';
            }

            /*
             * Validasi pertanyaan.
             */
            if ($soal === '') {
                $rowErrors[] = 'Soal kosong.';
            }

            /*
             * Pilihan A-E tetap wajib
             * seperti business process legacy.
             */
            if ($pila === '') {
                $rowErrors[] = 'Pilihan A kosong.';
            }

            if ($pilb === '') {
                $rowErrors[] = 'Pilihan B kosong.';
            }

            if ($pilc === '') {
                $rowErrors[] = 'Pilihan C kosong.';
            }

            if ($pild === '') {
                $rowErrors[] = 'Pilihan D kosong.';
            }

            if ($pile === '') {
                $rowErrors[] = 'Pilihan E kosong.';
            }

            /*
             * Kunci A-E.
             */
            if (!in_array($kunci, ['A', 'B', 'C', 'D', 'E'], true)) {
                $rowErrors[] = 'Kunci jawaban harus A, B, C, D, atau E.';
            }

            /*
             * Score legacy varchar(50).
             */
            if ($score === '') {
                $rowErrors[] = 'Score kosong.';
            } elseif (mb_strlen($score) > 50) {
                $rowErrors[] = 'Score melebihi 50 karakter.';
            }

            if ($rowErrors !== []) {
                $gagal++;

                $errors[] = 'Baris ' . $row . ': ' . implode(' ', $rowErrors);

                continue;
            }

            try {
                DB::transaction(function () use ($idSoal, $soal, $pila, $pilb, $pilc, $pild, $pile, $kunci, $score) {
                    $detail = new Detailsoal();

                    $detail->id_soal = $idSoal;

                    /*
                     * Kolom legacy NOT NULL.
                     */
                    $detail->jenis = '';

                    $detail->soal = $soal;

                    $detail->audio = null;

                    $detail->pila = $pila;

                    $detail->pilb = $pilb;

                    $detail->pilc = $pilc;

                    $detail->pild = $pild;

                    $detail->pile = $pile;

                    $detail->kunci = $kunci;

                    $detail->score = $score;

                    $detail->id_user = (string) auth()->id();

                    /*
                     * Sesuai import legacy:
                     * hasil import langsung tampil.
                     */
                    $detail->status = 'Y';

                    /*
                     * Import legacy tidak
                     * menggunakan sesi.
                     */
                    $detail->sesi = null;

                    $detail->save();
                });

                $sukses++;
            } catch (Throwable $exception) {
                report($exception);

                $gagal++;

                $errors[] = 'Baris ' . $row . ': gagal disimpan ke database.';
            }
        }

        /*
         * Lepaskan workbook dari memory.
         */
        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);

        $message = 'Import soal selesai. ' . 'Berhasil: ' . $sukses . ', Ditolak: ' . $gagal . '.';

        if ($kosong > 0) {
            $message .= ' Baris kosong dilewati: ' . $kosong . '.';
        }

        return redirect()->route('guru.soal')->with('success', $message)->with('import_errors', $errors);
    }

    /**
     * Authorization Paket Soal.
     *
     * Admin = seluruh paket.
     * Guru = hanya miliknya.
     */
    private function findAccessibleSoal(int $id): Soal {
        $query = Soal::query()->whereKey($id);

        if (auth()->user()->status === 'G') {
            $query->where('id_user', (string) auth()->id());
        }

        return $query->firstOrFail();
    }

    private function findAccessibleDetail(int $id): Detailsoal {
        $detail = Detailsoal::query()->whereKey($id)->firstOrFail();

        /*
         * Authorization mengikuti
         * kepemilikan paket induk.
         */
        $this->findAccessibleSoal((int) $detail->id_soal);

        return $detail;
    }

    private function spreadsheetCell(mixed $value): string {
        if ($value === null) {
            return '';
        }

        /*
         * Cell rich text.
         */
        if ($value instanceof RichText) {
            $value = $value->getPlainText();
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        /*
         * Angka ID/score tidak boleh
         * menjadi format scientific.
         */
        if (is_int($value) || is_float($value)) {
            return rtrim(rtrim(number_format($value, 10, '.', ''), '0'), '.');
        }

        return trim((string) $value);
    }
}
