<?php

namespace App\Http\Controllers;

use App\Models\Countexamtime;
use App\Models\Detailsoal;
use App\Models\Distribusisoal;
use App\Models\Jawab;
use App\Models\School;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiswaController extends Controller
{
    /**
     * Dashboard siswa.
     */
    public function index(): View
    {
        $user = $this->studentWithClass();

        $school = School::first();

        return view(
            'siswa.index',
            compact(
                'user',
                'school'
            )
        );
    }


    /**
     * Daftar ujian yang tersedia.
     */
    public function exams(): View
    {
        $user = $this->studentWithClass();

        $school = School::first();

        /*
         * Paket dengan jawaban final
         * dianggap sudah selesai.
         */
        $completedExamIds = Jawab::query()
            ->where(
                'id_user',
                auth()->id()
            )
            ->where(
                'status',
                'Y'
            )
            ->pluck('id_soal')
            ->map(
                fn ($id) =>
                    (string) $id
            )
            ->unique()
            ->values()
            ->all();


        if (
            empty($user->id_kelas)
        ) {
            $distribusisoal =
                collect();

            return view(
                'siswa.soal',
                compact(
                    'user',
                    'school',
                    'distribusisoal'
                )
            );
        }


        $query = Distribusisoal::query()
            ->join(
                'soals',
                'distribusisoals.id_soal',
                '=',
                'soals.id'
            )
            ->select(
                'distribusisoals.id',
                'distribusisoals.id_soal',
                'distribusisoals.id_kelas',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.waktu',
                'soals.jenis'
            )
            ->where(
                'distribusisoals.id_kelas',
                $user->id_kelas
            )
            ->where(
                'soals.jenis',
                '1'
            );


        if (
            $completedExamIds !== []
        ) {
            $query->whereNotIn(
                'soals.id',
                $completedExamIds
            );
        }


        $distribusisoal = $query
            ->orderByDesc(
                'distribusisoals.id'
            )
            ->get();


        return view(
            'siswa.soal',
            compact(
                'user',
                'school',
                'distribusisoal'
            )
        );
    }


    /**
     * Halaman persiapan + engine ujian.
     */
    public function exam(
        int $id
    ): View|RedirectResponse {
        $user = $this->studentWithClass();

        $school = School::first();

        $soal =
            $this->findDistributedExam(
                $id
            );


        if (
            $this->isExamFinished($soal->id)
        ) {
            return redirect()
                ->route('siswa.soal')
                ->with(
                    'error',
                    'Ujian tersebut sudah selesai.'
                );
        }


        /*
         * Hanya soal aktif/status Y
         * yang masuk ujian.
         */
        $availableIds = Detailsoal::query()
            ->where(
                'id_soal',
                $soal->id
            )
            ->where(
                'status',
                'Y'
            )
            ->pluck('id')
            ->map(
                fn ($id) =>
                    (int) $id
            )
            ->values();


        if (
            $availableIds->isEmpty()
        ) {
            return redirect()
                ->route('siswa.soal')
                ->with(
                    'error',
                    'Paket ujian belum memiliki soal aktif.'
                );
        }


        /*
         * Pertahankan urutan random selama
         * attempt berlangsung.
         *
         * Tidak mengubah database.
         */
        $sessionKey =
            $this->examOrderSessionKey(
                $soal->id
            );

        $questionOrder =
            session($sessionKey);

        $availableArray =
            $availableIds->all();


        if (
            ! $this->isQuestionOrderValid(
                $questionOrder,
                $availableArray
            )
        ) {
            $questionOrder =
                $availableIds
                    ->shuffle()
                    ->values()
                    ->all();

            session([
                $sessionKey =>
                    $questionOrder,
            ]);
        }


        /*
         * Nomor yang sudah dijawab,
         * tetapi belum final.
         */
        $answeredIds = Jawab::query()
            ->where(
                'id_soal',
                $soal->id
            )
            ->where(
                'id_user',
                auth()->id()
            )
            ->where(
                'status',
                'N'
            )
            ->pluck('no_soal_id')
            ->map(
                fn ($id) =>
                    (int) $id
            )
            ->values()
            ->all();


        $counter = Countexamtime::query()
            ->where(
                'id_soal',
                $soal->id
            )
            ->where(
                'id_user',
                auth()->id()
            )
            ->first();


        $hasStarted =
            $counter !== null;


        $remainingSeconds =
            $counter
                ? $this->previewRemainingSeconds(
                    $counter
                )
                : max(
                    0,
                    (int) $soal->waktu
                );


        return view(
            'siswa.detail_soal',
            compact(
                'user',
                'school',
                'soal',
                'questionOrder',
                'answeredIds',
                'hasStarted',
                'remainingSeconds'
            )
        );
    }


    /**
     * Mulai atau lanjutkan ujian.
     */
    public function startExam(
        int $id
    ): JsonResponse {
        $soal =
            $this->findDistributedExam(
                $id
            );


        if (
            $this->isExamFinished($soal->id)
        ) {
            return response()->json(
                [
                    'message' =>
                        'Ujian sudah selesai.',
                    'finished' => true,
                    'redirect' =>
                        route('siswa.soal'),
                ],
                409
            );
        }


        $jumlahSoal =
            Detailsoal::query()
                ->where(
                    'id_soal',
                    $soal->id
                )
                ->where(
                    'status',
                    'Y'
                )
                ->count();


        if ($jumlahSoal === 0) {
            return response()->json(
                [
                    'message' =>
                        'Paket ujian tidak memiliki soal aktif.',
                ],
                422
            );
        }


        $result = DB::transaction(
            function () use ($soal) {

                $counter =
                    Countexamtime::query()
                        ->where(
                            'id_soal',
                            $soal->id
                        )
                        ->where(
                            'id_user',
                            auth()->id()
                        )
                        ->lockForUpdate()
                        ->first();


                /*
                 * Attempt baru.
                 */
                if (! $counter) {

                    $counter =
                        new Countexamtime();

                    $counter->id_soal =
                        (string) $soal->id;

                    $counter->id_user =
                        (string) auth()->id();

                    $counter->waktu =
                        (string) max(
                            0,
                            (int) $soal->waktu
                        );

                    $counter->save();


                    return [
                        'remaining' =>
                            (int) $counter->waktu,

                        'expired' =>
                            false,
                    ];
                }


                /*
                 * Attempt lama / resume.
                 */
                $remaining =
                    $this->refreshCounter(
                        $counter
                    );


                if ($remaining <= 0) {

                    $this->finalizeExamRecords(
                        $soal
                    );

                    return [
                        'remaining' => 0,
                        'expired' => true,
                    ];
                }


                return [
                    'remaining' =>
                        $remaining,

                    'expired' =>
                        false,
                ];
            }
        );


        if ($result['expired']) {

            session()->forget(
                $this->examOrderSessionKey(
                    $soal->id
                )
            );

            return response()->json(
                [
                    'message' =>
                        'Waktu ujian telah habis.',

                    'expired' => true,

                    'remaining_seconds' =>
                        0,

                    'redirect' =>
                        route('siswa.soal'),
                ],
                409
            );
        }


        Log::info(
            'exam.started',
            [
                'user_id' =>
                    auth()->id(),

                'id_soal' =>
                    $soal->id,

                'remaining_seconds' =>
                    $result['remaining'],

                'ip' =>
                    request()->ip(),
            ]
        );


        return response()->json([
            'started' => true,

            'remaining_seconds' =>
                $result['remaining'],
        ]);
    }


    /**
     * Load satu soal melalui AJAX.
     */
    public function question(
        int $id
    ): View {
        $detailsoal =
            Detailsoal::query()
                ->whereKey($id)
                ->where(
                    'status',
                    'Y'
                )
                ->firstOrFail();


        $soal =
            $this->findDistributedExam(
                (int) $detailsoal->id_soal
            );


        if (
            $this->isExamFinished(
                $soal->id
            )
        ) {
            abort(
                409,
                'Ujian sudah selesai.'
            );
        }


        $counter =
            Countexamtime::query()
                ->where(
                    'id_soal',
                    $soal->id
                )
                ->where(
                    'id_user',
                    auth()->id()
                )
                ->first();


        if (! $counter) {
            abort(
                409,
                'Ujian belum dimulai.'
            );
        }


        if (
            $this->previewRemainingSeconds(
                $counter
            ) <= 0
        ) {
            abort(
                409,
                'Waktu ujian telah habis.'
            );
        }


        /*
         * Detail harus merupakan bagian dari
         * urutan attempt yang sedang aktif.
         */
        $questionOrder =
            session(
                $this->examOrderSessionKey(
                    $soal->id
                ),
                []
            );


        if (
            ! in_array(
                $detailsoal->id,
                $questionOrder,
                true
            )
        ) {
            abort(404);
        }


        $cekJawaban = Jawab::query()
            ->where(
                'no_soal_id',
                $detailsoal->id
            )
            ->where(
                'id_soal',
                $soal->id
            )
            ->where(
                'id_user',
                auth()->id()
            )
            ->where(
                'status',
                'N'
            )
            ->first();


        return view(
            'siswa.ajax.get_soal',
            compact(
                'detailsoal',
                'cekJawaban'
            )
        );
    }


    /**
     * Simpan / ubah jawaban siswa.
     */
    public function saveAnswer(
        Request $request
    ): JsonResponse {
        $validated =
            $request->validate([
                'pilihan' => [
                    'required',
                    Rule::in([
                        'A',
                        'B',
                        'C',
                        'D',
                        'E',
                    ]),
                ],

                'id_soal' => [
                    'required',
                    'integer',
                ],

                'no_soal_id' => [
                    'required',
                    'integer',
                ],
            ]);


        $soal =
            $this->findDistributedExam(
                (int)
                $validated['id_soal']
            );


        if (
            $this->isExamFinished(
                $soal->id
            )
        ) {
            return response()->json(
                [
                    'message' =>
                        'Ujian sudah selesai.',
                ],
                409
            );
        }


        /*
         * Pastikan detail benar-benar
         * milik paket tersebut.
         */
        $detail =
            Detailsoal::query()
                ->whereKey(
                    $validated[
                        'no_soal_id'
                    ]
                )
                ->where(
                    'id_soal',
                    $soal->id
                )
                ->where(
                    'status',
                    'Y'
                )
                ->firstOrFail();


        $result = DB::transaction(
            function () use (
                $soal,
                $detail,
                $validated
            ) {

                $counter =
                    Countexamtime::query()
                        ->where(
                            'id_soal',
                            $soal->id
                        )
                        ->where(
                            'id_user',
                            auth()->id()
                        )
                        ->lockForUpdate()
                        ->first();


                if (! $counter) {
                    return [
                        'not_started' =>
                            true,
                    ];
                }


                $remaining =
                    $this->refreshCounter(
                        $counter
                    );


                if ($remaining <= 0) {

                    $this->finalizeExamRecords(
                        $soal
                    );

                    return [
                        'expired' => true,
                        'remaining' => 0,
                    ];
                }


                $pilihan =
                    strtoupper(
                        $validated[
                            'pilihan'
                        ]
                    );


                $kunci =
                    strtoupper(
                        trim(
                            (string)
                            $detail->kunci
                        )
                    );


                $score =
                    $pilihan === $kunci
                        ? (string)
                            $detail->score
                        : '0';


                $user =
                    auth()->user();


                Jawab::query()
                    ->updateOrCreate(
                        [
                            'no_soal_id' =>
                                $detail->id,

                            'id_soal' =>
                                $soal->id,

                            'id_user' =>
                                $user->id,
                        ],
                        [
                            'id_kelas' =>
                                $user->id_kelas,

                            'nama' =>
                                $user->nama,

                            'pilihan' =>
                                $pilihan,

                            'score' =>
                                $score,

                            'status' =>
                                'N',
                        ]
                    );


                return [
                    'saved' => true,

                    'pilihan' =>
                        $pilihan,

                    'remaining' =>
                        $remaining,
                ];
            }
        );


        if (
            isset(
                $result['not_started']
            )
        ) {
            return response()->json(
                [
                    'message' =>
                        'Ujian belum dimulai.',
                ],
                409
            );
        }


        if (
            isset(
                $result['expired']
            )
        ) {
            session()->forget(
                $this->examOrderSessionKey(
                    $soal->id
                )
            );

            return response()->json(
                [
                    'message' =>
                        'Waktu ujian telah habis.',

                    'expired' => true,

                    'remaining_seconds' =>
                        0,

                    'redirect' =>
                        route('siswa.soal'),
                ],
                409
            );
        }


        return response()->json([
            'saved' => true,

            'pilihan' =>
                $result['pilihan'],

            'remaining_seconds' =>
                $result['remaining'],
        ]);
    }


    /**
     * Sinkronisasi timer server.
     *
     * URL legacy tetap:
     * POST /countexamtime
     */
    public function syncTime(
        Request $request
    ): JsonResponse {
        $validated =
            $request->validate([
                'id_soal' => [
                    'required',
                    'integer',
                ],
            ]);


        $soal =
            $this->findDistributedExam(
                (int)
                $validated['id_soal']
            );


        if (
            $this->isExamFinished(
                $soal->id
            )
        ) {
            return response()->json([
                'finished' => true,

                'remaining_seconds' =>
                    0,

                'redirect' =>
                    route('siswa.soal'),
            ]);
        }


        $result = DB::transaction(
            function () use ($soal) {

                $counter =
                    Countexamtime::query()
                        ->where(
                            'id_soal',
                            $soal->id
                        )
                        ->where(
                            'id_user',
                            auth()->id()
                        )
                        ->lockForUpdate()
                        ->first();


                if (! $counter) {
                    return [
                        'not_started' => true,
                    ];
                }


                $remaining =
                    $this->refreshCounter(
                        $counter
                    );


                if ($remaining <= 0) {

                    $this->finalizeExamRecords(
                        $soal
                    );

                    return [
                        'expired' => true,
                        'remaining' => 0,
                    ];
                }


                return [
                    'remaining' =>
                        $remaining,

                    'expired' =>
                        false,
                ];
            }
        );


        if (
            isset(
                $result['not_started']
            )
        ) {
            return response()->json(
                [
                    'message' =>
                        'Ujian belum dimulai.',
                ],
                409
            );
        }


        if (
            $result['expired']
        ) {
            session()->forget(
                $this->examOrderSessionKey(
                    $soal->id
                )
            );

            return response()->json([
                'expired' => true,

                'remaining_seconds' =>
                    0,

                'redirect' =>
                    route('siswa.soal'),
            ]);
        }


        return response()->json([
            'remaining_seconds' =>
                $result['remaining'],
        ]);
    }


    /**
     * Kirim/finalisasi seluruh jawaban.
     *
     * URL legacy tetap:
     * POST /kirimjawaban
     */
    public function finishExam(
        Request $request
    ): JsonResponse {
        $validated =
            $request->validate([
                'id_soal' => [
                    'required',
                    'integer',
                ],
            ]);


        $soal =
            $this->findDistributedExam(
                (int)
                $validated['id_soal']
            );


        if (
            $this->isExamFinished(
                $soal->id
            )
        ) {
            return response()->json([
                'finished' => true,

                'redirect' =>
                    route('siswa.soal'),
            ]);
        }


        $result = DB::transaction(
            function () use ($soal) {

                $counter =
                    Countexamtime::query()
                        ->where(
                            'id_soal',
                            $soal->id
                        )
                        ->where(
                            'id_user',
                            auth()->id()
                        )
                        ->lockForUpdate()
                        ->first();


                if (! $counter) {
                    return [
                        'not_started' =>
                            true,
                    ];
                }


                /*
                 * Sinkronisasi terakhir.
                 */
                $this->refreshCounter(
                    $counter
                );


                /*
                 * Finalisasi juga membuat
                 * record untuk soal yang
                 * tidak dijawab.
                 */
                $this->finalizeExamRecords(
                    $soal
                );


                $counter->waktu = '0';

                $counter->save();


                return [
                    'finished' => true,
                ];
            }
        );


        if (
            isset(
                $result['not_started']
            )
        ) {
            return response()->json(
                [
                    'message' =>
                        'Ujian belum dimulai.',
                ],
                409
            );
        }


        session()->forget(
            $this->examOrderSessionKey(
                $soal->id
            )
        );


        $score = Jawab::query()
            ->where(
                'id_soal',
                $soal->id
            )
            ->where(
                'id_user',
                auth()->id()
            )
            ->where(
                'status',
                'Y'
            )
            ->sum('score');


        Log::info(
            'exam.finished',
            [
                'user_id' =>
                    auth()->id(),

                'id_soal' =>
                    $soal->id,

                'score' =>
                    $score,

                'ip' =>
                    request()->ip(),
            ]
        );


        /*
         * Tahap berikutnya akan mengganti
         * redirect ini ke halaman hasil siswa.
         */
        return response()->json([
            'finished' => true,

            'score' =>
                $score,

            'redirect' =>
                route('siswa.soal'),
        ]);
    }


    /**
     * User + kelas.
     */
    private function studentWithClass(): User
    {
        return User::query()
            ->leftJoin(
                'kelas',
                'users.id_kelas',
                '=',
                'kelas.id'
            )
            ->select(
                'users.*',
                'kelas.nama as nama_kelas'
            )
            ->where(
                'users.id',
                auth()->id()
            )
            ->firstOrFail();
    }


    /**
     * Paket harus merupakan ujian
     * yang didistribusikan ke kelas siswa.
     */
    private function findDistributedExam(
        int $id
    ): Soal {
        $user =
            auth()->user();


        if (
            empty($user->id_kelas)
        ) {
            abort(404);
        }


        return Soal::query()
            ->whereKey($id)
            ->where(
                'jenis',
                '1'
            )
            ->whereHas(
                'distribusisoals',
                function ($query) use ($user) {

                    $query->where(
                        'id_kelas',
                        $user->id_kelas
                    );

                }
            )
            ->firstOrFail();
    }


    /**
     * Sudah final?
     */
    private function isExamFinished(
        int $idSoal
    ): bool {
        return Jawab::query()
            ->where(
                'id_soal',
                $idSoal
            )
            ->where(
                'id_user',
                auth()->id()
            )
            ->where(
                'status',
                'Y'
            )
            ->exists();
    }


    /**
     * Sisa waktu tanpa mengubah DB.
     */
    private function previewRemainingSeconds(
        Countexamtime $counter
    ): int {
        $remaining =
            max(
                0,
                (int) $counter->waktu
            );


        if (
            ! $counter->updated_at
        ) {
            return $remaining;
        }


        $elapsed =
            max(
                0,
                now()->timestamp -
                $counter->updated_at->timestamp
            );


        return max(
            0,
            $remaining - $elapsed
        );
    }


    /**
     * Sinkronisasi sisa waktu
     * sekaligus update checkpoint DB.
     */
    private function refreshCounter(
        Countexamtime $counter
    ): int {
        $remaining =
            $this->previewRemainingSeconds(
                $counter
            );


        $counter->waktu =
            (string) $remaining;

        $counter->save();


        return $remaining;
    }


    /**
     * Finalisasi seluruh soal.
     *
     * Soal yang tidak dijawab tetap dibuat
     * dengan pilihan kosong + score 0.
     */
    private function finalizeExamRecords(
        Soal $soal
    ): void {
        $user =
            auth()->user();


        $details = Detailsoal::query()
            ->where(
                'id_soal',
                $soal->id
            )
            ->where(
                'status',
                'Y'
            )
            ->get();


        foreach (
            $details
            as $detail
        ) {
            $jawab = Jawab::query()
                ->firstOrNew([
                    'no_soal_id' =>
                        $detail->id,

                    'id_soal' =>
                        $soal->id,

                    'id_user' =>
                        $user->id,
                ]);


            $pilihan =
                strtoupper(
                    trim(
                        (string)
                        $jawab->pilihan
                    )
                );


            $kunci =
                strtoupper(
                    trim(
                        (string)
                        $detail->kunci
                    )
                );


            $jawab->id_kelas =
                $user->id_kelas;

            $jawab->nama =
                $user->nama;

            /*
             * Belum dijawab = string kosong.
             */
            $jawab->pilihan =
                $pilihan;

            /*
             * Score dihitung ulang server-side
             * saat finalisasi.
             */
            $jawab->score =
                $pilihan !== '' &&
                $pilihan === $kunci
                    ? (string)
                        $detail->score
                    : '0';

            $jawab->status =
                'Y';

            $jawab->save();
        }
    }


    /**
     * Key urutan soal di session.
     */
    private function examOrderSessionKey(
        int $idSoal
    ): string {
        return
            'exam_order.'.
            auth()->id().
            '.'.
            $idSoal;
    }


    /**
     * Validasi urutan soal dari session.
     */
    private function isQuestionOrderValid(
        mixed $order,
        array $available
    ): bool {
        if (! is_array($order)) {
            return false;
        }


        if (
            count($order) !==
            count($available)
        ) {
            return false;
        }


        $orderCopy =
            array_map(
                'intval',
                $order
            );

        $availableCopy =
            array_map(
                'intval',
                $available
            );


        sort($orderCopy);

        sort($availableCopy);


        return
            $orderCopy ===
            $availableCopy;
    }
}