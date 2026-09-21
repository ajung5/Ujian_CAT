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

class HasilController extends Controller
{
    /**
     * Daftar paket yang sudah memiliki hasil.
     *
     * Admin:
     * - dapat melihat seluruh paket.
     *
     * Guru:
     * - hanya paket miliknya sendiri.
     */
    public function index(): View{
        $user = User::findOrFail(
            auth()->id()
        );

        $school = School::first();

        $jawabs = $this->resultsQuery()
            ->paginate(10);

        return view(
            'guru.hasil',
            compact(
                'user',
                'school',
                'jawabs'
            )
        );
    }

    /**
     * Search laporan melalui AJAX.
     */
    public function search(Request $request): View{
        $validated = $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:150',
            ],
        ]);

        $q = trim(
            (string) ($validated['q'] ?? '')
        );

        $jawabs = $this->resultsQuery($q)
            ->limit(50)
            ->get();

        return view(
            'guru.ajax.get_hasil_guru',
            compact('jawabs')
        );
    }

    public function classDetail(int $id,int $idSoal): View {
        $user = User::findOrFail(
            auth()->id()
        );

        $school = School::first();

        $soal = $this->findAccessiblePackage(
            $idSoal
        );

        $kelas = Kelas::findOrFail(
            $id
        );

        $jawabs = Jawab::query()
            ->join(
                'users',
                'jawabs.id_user',
                '=',
                'users.id'
            )
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
                    "
                ),

                DB::raw(
                    'MAX(jawabs.updated_at) as selesai_pada'
                )
            )
            ->where(
                'jawabs.id_kelas',
                $kelas->id
            )
            ->where(
                'jawabs.id_soal',
                $soal->id
            )
            ->where(
                'jawabs.status',
                'Y'
            )
            ->groupBy(
                'jawabs.id_user',
                'jawabs.id_kelas',
                'jawabs.id_soal',
                'users.nama',
                'users.no_induk'
            )
            ->orderBy(
                'users.nama'
            )
            ->get();

        $participantIds =
            $jawabs
                ->pluck('id_user')
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->all();

        $detailJawabs = collect();

        if ($participantIds !== []) {

            $detailJawabs =
                Jawab::query()
                    ->join(
                        'detailsoals',
                        'jawabs.no_soal_id',
                        '=',
                        'detailsoals.id'
                    )
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
                        'detailsoals.score as max_score'
                    )
                    ->where(
                        'jawabs.id_kelas',
                        $kelas->id
                    )
                    ->where(
                        'jawabs.id_soal',
                        $soal->id
                    )
                    ->where(
                        'jawabs.status',
                        'Y'
                    )
                    ->whereIn(
                        'jawabs.id_user',
                        $participantIds
                    )
                    ->orderBy(
                        'jawabs.id_user'
                    )
                    ->orderBy(
                        'jawabs.no_soal_id'
                    )
                    ->get();
        }

        $answersByUser =
            $detailJawabs->groupBy(
                'id_user'
            );

        return view(
            'guru.detailhasilsoal',
            compact(
                'user',
                'school',
                'soal',
                'kelas',
                'jawabs',
                'answersByUser'
            )
        );
    }

    public function destroyStudentResult(Request $request): JsonResponse {
        $validated =
            $request->validate([
                'id_kelas' => [
                    'required',
                    'integer',
                ],

                'id_soal' => [
                    'required',
                    'integer',
                ],

                'id_user' => [
                    'required',
                    'integer',
                ],
            ]);

        $soal =
            $this->findAccessiblePackage(
                (int) $validated['id_soal']
            );

        $kelas =
            Kelas::findOrFail(
                (int) $validated['id_kelas']
            );

        $student =
            User::query()
                ->whereKey(
                    (int) $validated['id_user']
                )
                ->where(
                    'id_kelas',
                    $kelas->id
                )
                ->firstOrFail();

        $deleted =
            DB::transaction(
                function () use (
                    $soal,
                    $kelas,
                    $student
                ) {

                    $deleted =
                        Jawab::query()
                            ->where(
                                'id_soal',
                                $soal->id
                            )
                            ->where(
                                'id_kelas',
                                $kelas->id
                            )
                            ->where(
                                'id_user',
                                $student->id
                            )
                            ->delete();

                    Countexamtime::query()
                        ->where(
                            'id_soal',
                            $soal->id
                        )
                        ->where(
                            'id_user',
                            $student->id
                        )
                        ->delete();

                    return $deleted;
                }
            );

        Aktifitas::create([
            'id_user' =>
                auth()->id(),

            'nama' =>
                'Menghapus hasil '.
                strtolower(
                    $this->assessmentLabel(
                        $soal
                    )
                ).
                ' siswa '.
                $student->nama.
                ' pada paket '.
                $soal->paket.'.',
        ]);

        return response()->json([
            'success' => true,

            'deleted' =>
                $deleted,

            'message' =>
                'Hasil siswa berhasil dihapus.',
        ]);
    }

    public function destroyClassResults(Request $request): JsonResponse {
        $validated =
            $request->validate([
                'id_kelas' => [
                    'required',
                    'integer',
                ],

                'id_soal' => [
                    'required',
                    'integer',
                ],
            ]);

        $soal =
            $this->findAccessiblePackage(
                (int) $validated['id_soal']
            );

        $kelas =
            Kelas::findOrFail(
                (int) $validated['id_kelas']
            );

        $deleted =
            DB::transaction(
                function () use (
                    $soal,
                    $kelas
                ) {

                    $studentIds =
                        Jawab::query()
                            ->where(
                                'id_soal',
                                $soal->id
                            )
                            ->where(
                                'id_kelas',
                                $kelas->id
                            )
                            ->pluck(
                                'id_user'
                            )
                            ->unique()
                            ->values()
                            ->all();

                    $deleted =
                        Jawab::query()
                            ->where(
                                'id_soal',
                                $soal->id
                            )
                            ->where(
                                'id_kelas',
                                $kelas->id
                            )
                            ->delete();

                    if (
                        $studentIds !== []
                    ) {
                        Countexamtime::query()
                            ->where(
                                'id_soal',
                                $soal->id
                            )
                            ->whereIn(
                                'id_user',
                                $studentIds
                            )
                            ->delete();
                    }

                    return $deleted;
                }
            );

        Aktifitas::create([
            'id_user' =>
                auth()->id(),

            'nama' =>
                'Menghapus seluruh hasil '.
                strtolower(
                    $this->assessmentLabel(
                        $soal
                    )
                ).
                ' kelas '.
                $kelas->nama.
                ' pada paket '.
                $soal->paket.'.',
        ]);

        return response()->json([
            'success' => true,

            'deleted' =>
                $deleted,

            'message' =>
                'Hasil kelas berhasil dihapus.',
        ]);
    }

    public function detail(int $id): View{
        $user = User::findOrFail(
            auth()->id()
        );

        $school = School::first();

        $soal = $this->findAccessiblePackage(
            $id
        );

        $jawabs = Jawab::query()
            ->join(
                'kelas',
                'jawabs.id_kelas',
                '=',
                'kelas.id'
            )
            ->select(
                'kelas.id as id_kelas',
                'kelas.nama as nama_kelas',
                'jawabs.id_soal',

                DB::raw(
                    'COUNT(DISTINCT jawabs.id_user) as jumlah_peserta'
                ),

                DB::raw(
                    'MAX(jawabs.updated_at) as terakhir_dikerjakan'
                )
            )
            ->where(
                'jawabs.id_soal',
                $soal->id
            )
            ->where(
                'jawabs.status',
                'Y'
            )
            ->whereNotNull(
                'jawabs.id_kelas'
            )
            ->groupBy(
                'kelas.id',
                'kelas.nama',
                'jawabs.id_soal'
            )
            ->orderBy(
                'kelas.nama'
            )
            ->paginate(15);

        $aktifitas =
            $this->recentActivities();

        return view(
            'guru.detailhasil',
            compact(
                'user',
                'school',
                'soal',
                'jawabs',
                'aktifitas'
            )
        );
    }

    /**
     * Query dasar laporan.
     *
     * Hanya jawaban final status=Y yang
     * dianggap sebagai hasil pengerjaan.
     */
    private function resultsQuery(?string $search = null): Builder {
        $query = Jawab::query()
            ->join(
                'soals',
                'jawabs.id_soal',
                '=',
                'soals.id'
            )
            ->select(
                'soals.id as id_soal',
                'soals.id_user',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.waktu',
                'soals.jenis',
                'soals.created_at',

                \DB::raw(
                    'COUNT(DISTINCT jawabs.id_user) as jumlah_peserta'
                ),

                \DB::raw(
                    'MAX(jawabs.updated_at) as terakhir_dikerjakan'
                )
            )
            ->where(
                'jawabs.status',
                'Y'
            );

        /*
         * Guru hanya boleh melihat
         * hasil paket yang dibuat olehnya.
         *
         * Admin dapat melihat semua.
         */
        if (
            auth()->user()->status === 'G'
        ) {
            $query->where(
                'soals.id_user',
                auth()->id()
            );
        }

        if (
            $search !== null &&
            $search !== ''
        ) {
            $query->where(
                'soals.paket',
                'like',
                '%'.$search.'%'
            );
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
                'soals.created_at'
            )
            ->orderByDesc(
                'terakhir_dikerjakan'
            );
    }

    private function findAccessiblePackage(int $id): Soal {
        return Soal::query()
            ->whereKey($id)
            ->when(
                auth()->user()->status === 'G',

                fn ($query) =>
                    $query->where(
                        'id_user',
                        auth()->id()
                    )
            )
            ->firstOrFail();
    }

    private function recentActivities(){
    return Aktifitas::query()
        ->join(
            'users',
            'aktifitas.id_user',
            '=',
            'users.id'
        )
        ->select(
            'users.nama as nama_user',
            'users.gambar',
            'aktifitas.id',
            'aktifitas.id_user',
            'aktifitas.nama',
            'aktifitas.created_at',
            'aktifitas.updated_at'
        )
        ->orderByDesc(
            'aktifitas.id'
        )
        ->limit(3)
        ->get();
    }
}