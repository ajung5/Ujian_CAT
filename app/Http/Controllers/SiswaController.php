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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiswaController extends Controller {
    /**
     * Dashboard siswa.
     */
    public function index(): View {
        $user = $this->studentWithClass();
        $school = School::first();

        return view('siswa.index', compact('user', 'school'));
    }

    /**
     * Profil siswa.
     */
    public function profile(): View {
        $user = $this->studentWithClass();
        $school = School::first();

        return view('siswa.profil', compact('user', 'school'));
    }

    /**
     * Upload foto profil siswa.
     * Legacy: JPG/JPEG/PNG, maksimal 1 MB.
     */
    public function updateProfilePhoto(Request $request): JsonResponse {
        $request->validate(
            [
                'file' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            ],
            [
                'file.required' => 'Foto belum dipilih.',
                'file.image' => 'File harus berupa gambar.',
                'file.mimes' => 'Foto harus berupa JPG, JPEG, atau PNG.',
                'file.max' => 'Ukuran foto maksimal 1 MB.',
            ],
        );

        $user = User::query()
            ->whereKey(Auth::id())
            ->whereIn('status', ['S', 'C'])
            ->firstOrFail();

        $file = $request->file('file');
        $extension = strtolower($file->extension());
        $filename = Str::uuid() . '.' . $extension;

        File::ensureDirectoryExists(public_path('img'));
        $file->move(public_path('img'), $filename);

        $oldFilename = !empty($user->gambar) ? basename($user->gambar) : null;

        $user->gambar = $filename;
        $user->save();

        if ($oldFilename && $oldFilename !== 'siswa.png') {
            $oldImage = public_path('img/' . $oldFilename);

            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }
        }

        Log::info('student.profile.photo.updated', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui.',
            'filename' => $filename,
            'url' => asset('img/' . $filename),
        ]);
    }

    /**
     * Daftar ujian yang tersedia untuk kelas siswa.
     */
    public function exams(): View {
        $user = $this->studentWithClass();
        $school = School::first();

        $completedExamIds = Jawab::query()
            ->where('id_user', (string) Auth::id())
            ->where('status', 'Y')
            ->pluck('id_soal')
            ->map(fn($id) => (string) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($user->id_kelas)) {
            $distribusisoal = collect();

            return view('siswa.soal', compact('user', 'school', 'distribusisoal'));
        }

        $query = Distribusisoal::query()
            ->join('soals', 'distribusisoals.id_soal', '=', 'soals.id')
            ->select(
                'distribusisoals.id',
                'distribusisoals.id_soal',
                'distribusisoals.id_kelas',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.waktu',
                'soals.jenis',
            )
            ->where('distribusisoals.id_kelas', (string) $user->id_kelas)
            ->where('soals.jenis', '1');

        if ($completedExamIds !== []) {
            $query->whereNotIn('soals.id', $completedExamIds);
        }

        $distribusisoal = $query->orderByDesc('distribusisoals.id')->get();

        return view('siswa.soal', compact('user', 'school', 'distribusisoal'));
    }

    /**
     * Halaman engine ujian.
     */
    public function exam(int $id): View|RedirectResponse {
        $soal = $this->findDistributedExam($id);

        return $this->assessmentPage($soal, 'Ujian', route('siswa.soal'), route('siswa.exam.start', $soal->id));
    }

    /**
     * Halaman engine latihan.
     */
    public function training(int $id): View|RedirectResponse {
        $soal = $this->findTraining($id);

        return $this->assessmentPage(
            $soal,
            'Latihan',
            route('siswa.latihan'),
            route('siswa.training.start', $soal->id),
        );
    }

    /**
     * Halaman engine bersama untuk Ujian dan Latihan.
     */
    private function assessmentPage(
        Soal $soal,
        string $assessmentLabel,
        string $backUrl,
        string $startUrl,
    ): View|RedirectResponse {
        $user = $this->studentWithClass();
        $school = School::first();

        if ($this->isExamFinished($soal->id)) {
            return redirect()
                ->route('siswa.results')
                ->with('error', $assessmentLabel . ' tersebut sudah selesai.');
        }

        $availableIds = Detailsoal::query()
            ->where('id_soal', (string) $soal->id)
            ->where('status', 'Y')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values();

        if ($availableIds->isEmpty()) {
            return redirect($backUrl)->with(
                'error',
                'Paket ' . strtolower($assessmentLabel) . ' belum memiliki soal aktif.',
            );
        }

        $sessionKey = $this->examOrderSessionKey($soal->id);
        $questionOrder = session($sessionKey);
        $availableArray = $availableIds->all();

        if (!$this->isQuestionOrderValid($questionOrder, $availableArray)) {
            $questionOrder = $availableIds->shuffle()->values()->all();

            session([
                $sessionKey => $questionOrder,
            ]);
        }

        $answeredIds = Jawab::query()
            ->where('id_soal', (string) $soal->id)
            ->where('id_user', (string) Auth::id())
            ->where('status', 'N')
            ->pluck('no_soal_id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        $counter = Countexamtime::query()
            ->where('id_soal', (string) $soal->id)
            ->where('id_user', (string) Auth::id())
            ->first();

        $hasStarted = $counter !== null;

        $remainingSeconds = $counter ? $this->previewRemainingSeconds($counter) : max(0, (int) $soal->waktu);

        $isTraining = (string) $soal->jenis === '2';

        return view(
            'siswa.detail_soal',
            compact(
                'user',
                'school',
                'soal',
                'questionOrder',
                'answeredIds',
                'hasStarted',
                'remainingSeconds',
                'assessmentLabel',
                'backUrl',
                'startUrl',
                'isTraining',
            ),
        );
    }

    /**
     * Mulai / lanjutkan Ujian atau Latihan.
     */
    public function startExam(int $id): JsonResponse {
        $soal = $this->findAccessiblePackage($id);
        $assessmentLabel = $this->assessmentLabel($soal);

        if ($this->isExamFinished($soal->id)) {
            return response()->json(
                [
                    'message' => $assessmentLabel . ' sudah selesai.',
                    'finished' => true,
                    'redirect' => route('siswa.results'),
                ],
                409,
            );
        }

        $jumlahSoal = Detailsoal::query()->where('id_soal', (string) $soal->id)->where('status', 'Y')->count();

        if ($jumlahSoal === 0) {
            return response()->json(
                [
                    'message' => 'Paket ' . strtolower($assessmentLabel) . ' tidak memiliki soal aktif.',
                ],
                422,
            );
        }

        $result = DB::transaction(function () use ($soal) {
            $counter = Countexamtime::query()
                ->where('id_soal', (string) $soal->id)
                ->where('id_user', (string) Auth::id())
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                $counter = new Countexamtime();
                $counter->id_soal = (string) $soal->id;
                $counter->id_user = (string) Auth::id();
                $counter->waktu = (string) max(0, (int) $soal->waktu);
                $counter->save();

                return [
                    'remaining' => (int) $counter->waktu,
                    'expired' => false,
                ];
            }

            $remaining = $this->refreshCounter($counter);

            if ($remaining <= 0) {
                $this->finalizeExamRecords($soal);

                return [
                    'remaining' => 0,
                    'expired' => true,
                ];
            }

            return [
                'remaining' => $remaining,
                'expired' => false,
            ];
        });

        if ($result['expired']) {
            session()->forget($this->examOrderSessionKey($soal->id));

            return response()->json(
                [
                    'message' => 'Waktu ' . strtolower($assessmentLabel) . ' telah habis.',
                    'expired' => true,
                    'remaining_seconds' => 0,
                    'redirect' => route('siswa.results'),
                ],
                409,
            );
        }

        Log::info('assessment.started', [
            'type' => (string) $soal->jenis,
            'user_id' => Auth::id(),
            'id_soal' => $soal->id,
            'remaining_seconds' => $result['remaining'],
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'started' => true,
            'remaining_seconds' => $result['remaining'],
        ]);
    }

    /**
     * Load satu soal melalui AJAX.
     */
    public function question(int $id): View {
        $detailsoal = Detailsoal::query()->whereKey($id)->where('status', 'Y')->firstOrFail();

        $soal = $this->findAccessiblePackage((int) $detailsoal->id_soal);

        $assessmentLabel = $this->assessmentLabel($soal);

        if ($this->isExamFinished($soal->id)) {
            abort(409, $assessmentLabel . ' sudah selesai.');
        }

        $counter = Countexamtime::query()
            ->where('id_soal', (string) $soal->id)
            ->where('id_user', (string) Auth::id())
            ->first();

        if (!$counter) {
            abort(409, $assessmentLabel . ' belum dimulai.');
        }

        if ($this->previewRemainingSeconds($counter) <= 0) {
            abort(409, 'Waktu ' . strtolower($assessmentLabel) . ' telah habis.');
        }

        $questionOrder = session($this->examOrderSessionKey($soal->id), []);

        if (!in_array((int) $detailsoal->id, array_map('intval', $questionOrder), true)) {
            abort(404);
        }

        $cekJawaban = Jawab::query()
            ->where('no_soal_id', (string) $detailsoal->id)
            ->where('id_soal', (string) $soal->id)
            ->where('id_user', (string) Auth::id())
            ->where('status', 'N')
            ->first();

        return view('siswa.ajax.get_soal', compact('detailsoal', 'cekJawaban'));
    }

    /**
     * Simpan / ubah jawaban.
     */
    public function saveAnswer(Request $request): JsonResponse {
        $validated = $request->validate([
            'pilihan' => ['required', Rule::in(['A', 'B', 'C', 'D', 'E'])],
            'id_soal' => ['required', 'integer'],
            'no_soal_id' => ['required', 'integer'],
        ]);

        $soal = $this->findAccessiblePackage((int) $validated['id_soal']);

        $assessmentLabel = $this->assessmentLabel($soal);

        if ($this->isExamFinished($soal->id)) {
            return response()->json(
                [
                    'message' => $assessmentLabel . ' sudah selesai.',
                ],
                409,
            );
        }

        $detail = Detailsoal::query()
            ->whereKey($validated['no_soal_id'])
            ->where('id_soal', (string) $soal->id)
            ->where('status', 'Y')
            ->firstOrFail();

        $questionOrder = session($this->examOrderSessionKey($soal->id), []);

        if (!in_array((int) $detail->id, array_map('intval', $questionOrder), true)) {
            abort(404);
        }

        $result = DB::transaction(function () use ($soal, $detail, $validated) {
            $counter = Countexamtime::query()
                ->where('id_soal', (string) $soal->id)
                ->where('id_user', (string) Auth::id())
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                return [
                    'not_started' => true,
                ];
            }

            $remaining = $this->refreshCounter($counter);

            if ($remaining <= 0) {
                $this->finalizeExamRecords($soal);

                return [
                    'expired' => true,
                    'remaining' => 0,
                ];
            }

            $pilihan = strtoupper($validated['pilihan']);
            $kunci = strtoupper(trim((string) $detail->kunci));

            $score = $pilihan === $kunci ? (string) $detail->score : '0';

            $user = Auth::user();

            Jawab::query()->updateOrCreate(
                [
                    'no_soal_id' => (string) $detail->id,
                    'id_soal' => (string) $soal->id,
                    'id_user' => (string) $user->id,
                ],
                [
                    'id_kelas' => (string) $user->id_kelas,
                    'nama' => $user->nama,
                    'pilihan' => $pilihan,
                    'score' => $score,
                    'status' => 'N',
                ],
            );

            return [
                'saved' => true,
                'pilihan' => $pilihan,
                'remaining' => $remaining,
            ];
        });

        if (isset($result['not_started'])) {
            return response()->json(
                [
                    'message' => $assessmentLabel . ' belum dimulai.',
                ],
                409,
            );
        }

        if (isset($result['expired'])) {
            session()->forget($this->examOrderSessionKey($soal->id));

            return response()->json(
                [
                    'message' => 'Waktu ' . strtolower($assessmentLabel) . ' telah habis.',
                    'expired' => true,
                    'remaining_seconds' => 0,
                    'redirect' => route('siswa.results'),
                ],
                409,
            );
        }

        return response()->json([
            'saved' => true,
            'pilihan' => $result['pilihan'],
            'remaining_seconds' => $result['remaining'],
        ]);
    }

    /**
     * Sinkronisasi timer server.
     */
    public function syncTime(Request $request): JsonResponse {
        $validated = $request->validate([
            'id_soal' => ['required', 'integer'],
        ]);

        $soal = $this->findAccessiblePackage((int) $validated['id_soal']);

        $assessmentLabel = $this->assessmentLabel($soal);

        if ($this->isExamFinished($soal->id)) {
            return response()->json([
                'finished' => true,
                'remaining_seconds' => 0,
                'redirect' => route('siswa.results'),
            ]);
        }

        $result = DB::transaction(function () use ($soal) {
            $counter = Countexamtime::query()
                ->where('id_soal', (string) $soal->id)
                ->where('id_user', (string) Auth::id())
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                return [
                    'not_started' => true,
                ];
            }

            $remaining = $this->refreshCounter($counter);

            if ($remaining <= 0) {
                $this->finalizeExamRecords($soal);

                return [
                    'expired' => true,
                    'remaining' => 0,
                ];
            }

            return [
                'remaining' => $remaining,
                'expired' => false,
            ];
        });

        if (isset($result['not_started'])) {
            return response()->json(
                [
                    'message' => $assessmentLabel . ' belum dimulai.',
                ],
                409,
            );
        }

        if ($result['expired']) {
            session()->forget($this->examOrderSessionKey($soal->id));

            return response()->json([
                'expired' => true,
                'remaining_seconds' => 0,
                'redirect' => route('siswa.results'),
            ]);
        }

        return response()->json([
            'remaining_seconds' => $result['remaining'],
        ]);
    }

    /**
     * Finalisasi seluruh jawaban.
     */
    public function finishExam(Request $request): JsonResponse {
        $validated = $request->validate([
            'id_soal' => ['required', 'integer'],
        ]);

        $soal = $this->findAccessiblePackage((int) $validated['id_soal']);

        $assessmentLabel = $this->assessmentLabel($soal);

        if ($this->isExamFinished($soal->id)) {
            return response()->json([
                'finished' => true,
                'redirect' => route('siswa.results'),
            ]);
        }

        $result = DB::transaction(function () use ($soal) {
            $counter = Countexamtime::query()
                ->where('id_soal', (string) $soal->id)
                ->where('id_user', (string) Auth::id())
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                return [
                    'not_started' => true,
                ];
            }

            $this->refreshCounter($counter);
            $this->finalizeExamRecords($soal);

            $counter->waktu = '0';
            $counter->save();

            return [
                'finished' => true,
            ];
        });

        if (isset($result['not_started'])) {
            return response()->json(
                [
                    'message' => $assessmentLabel . ' belum dimulai.',
                ],
                409,
            );
        }

        session()->forget($this->examOrderSessionKey($soal->id));

        $score = Jawab::query()
            ->where('id_soal', (string) $soal->id)
            ->where('id_user', (string) Auth::id())
            ->where('status', 'Y')
            ->sum('score');

        Log::info('assessment.finished', [
            'type' => (string) $soal->jenis,
            'user_id' => Auth::id(),
            'id_soal' => $soal->id,
            'score' => $score,
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'finished' => true,
            'score' => $score,
            'redirect' => route('siswa.results'),
        ]);
    }

    /**
     * Daftar hasil Ujian dan Latihan siswa.
     */
    public function results(): View {
        $user = $this->studentWithClass();
        $school = School::first();

        $results = $this->studentResultsQuery()->paginate(10);

        return view('siswa.hasil', compact('user', 'school', 'results'));
    }

    /**
     * Pencarian hasil via AJAX.
     */
    public function searchResults(Request $request): View {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
        ]);

        $q = trim((string) ($validated['q'] ?? ''));

        $results = $this->studentResultsQuery($q)->limit(50)->get();

        return view('siswa.ajax.get_hasil', compact('results'));
    }

    /**
     * Review detail hasil.
     */
    public function resultDetail(int $id): View|RedirectResponse {
        $user = $this->studentWithClass();
        $school = School::first();

        $soal = Soal::query()->whereKey($id)->firstOrFail();

        if ((string) $soal->jenis !== '2') {
            Log::warning('assessment.review.denied', [
                'reason' => 'review_not_available_for_exam',
                'type' => (string) $soal->jenis,
                'user_id' => Auth::id(),
                'id_soal' => $soal->id,
                'ip' => request()->ip(),
            ]);

            return redirect()
                ->route('siswa.results')
                ->with('error', 'Review jawaban hanya tersedia untuk tipe Latihan.');
        }
        $hasFinalAnswer = Jawab::query()
            ->where('id_soal', (string) $soal->id)
            ->where('id_user', (string) Auth::id())
            ->where('status', 'Y')
            ->exists();

        $hasDraftAnswer = Jawab::query()
            ->where('id_soal', (string) $soal->id)
            ->where('id_user', (string) Auth::id())
            ->where('status', 'N')
            ->exists();

        if (!$hasFinalAnswer || $hasDraftAnswer) {
            Log::warning('assessment.review.denied', [
                'user_id' => Auth::id(),
                'id_soal' => $soal->id,
                'has_finished_answer' => $hasFinalAnswer,
                'has_draft_answer' => $hasDraftAnswer,
                'ip' => request()->ip(),
            ]);

            return redirect()
                ->route('siswa.results')
                ->with('error', 'Review jawaban hanya tersedia setelah pengerjaan selesai.');
        }

        $jawabs = Detailsoal::query()
            ->leftJoin('jawabs', function ($join) use ($soal) {
                $join
                    ->on('detailsoals.id', '=', 'jawabs.no_soal_id')
                    ->where('jawabs.id_soal', '=', (string) $soal->id)
                    ->where('jawabs.id_user', '=', (string) Auth::id())
                    ->where('jawabs.status', '=', 'Y');
            })
            ->select(
                'detailsoals.id as detail_id',
                'detailsoals.soal',
                'detailsoals.audio',
                'detailsoals.pila',
                'detailsoals.pilb',
                'detailsoals.pilc',
                'detailsoals.pild',
                'detailsoals.pile',
                'detailsoals.kunci',
                'detailsoals.score as max_score',
                'jawabs.pilihan as jawaban',
                'jawabs.score as score_diperoleh',
                'jawabs.created_at as dijawab_pada',
                'jawabs.updated_at as diubah_pada',
            )
            ->where('detailsoals.id_soal', (string) $soal->id)
            ->orderBy('detailsoals.id')
            ->get();

        $jumlahSoal = $jawabs->count();
        $benar = 0;
        $salah = 0;
        $tidakDijawab = 0;
        $nilai = 0.0;

        foreach ($jawabs as $jawab) {
            $pilihan = strtoupper(trim((string) $jawab->getAttribute('jawaban')));

            $kunci = strtoupper(trim((string) $jawab->kunci));

            $nilai += (float) ($jawab->getAttribute('score_diperoleh') ?? 0);

            if ($pilihan === '') {
                $tidakDijawab++;
            } elseif ($pilihan === $kunci) {
                $benar++;
            } else {
                $salah++;
            }
        }

        $lulus = $nilai >= (float) $soal->kkm;
        $jenis = $this->assessmentLabel($soal);

        Log::info('assessment.review.opened', [
            'type' => (string) $soal->jenis,
            'user_id' => Auth::id(),
            'id_soal' => $soal->id,
            'score' => $nilai,
            'correct' => $benar,
            'wrong' => $salah,
            'unanswered' => $tidakDijawab,
            'ip' => request()->ip(),
        ]);

        return view(
            'siswa.detail',
            compact(
                'user',
                'school',
                'soal',
                'jawabs',
                'jumlahSoal',
                'benar',
                'salah',
                'tidakDijawab',
                'nilai',
                'lulus',
                'jenis',
            ),
        );
    }

    /**
     * Query agregasi hasil milik siswa.
     */

    /**
     * @return Builder<Jawab>
     */
    private function studentResultsQuery(?string $search = null) {
        $query = Jawab::query()
            ->join('soals', 'jawabs.id_soal', '=', 'soals.id')
            ->select(
                'soals.id as id_soal',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.jenis as jenis_soal',
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
                DB::raw('MAX(jawabs.updated_at) as completed_at'),
            )
            ->where('jawabs.id_user', (string) Auth::id())
            ->where('jawabs.status', 'Y');

        if ($search !== null && $search !== '') {
            $query->where('soals.paket', 'like', '%' . $search . '%');
        }

        return $query
            ->groupBy('soals.id', 'soals.paket', 'soals.deskripsi', 'soals.kkm', 'soals.jenis')
            ->orderByDesc('completed_at');
    }

    /**
     * User siswa + nama kelas.
     */
    private function studentWithClass(): User {
        return User::query()
            ->leftJoin('kelas', 'users.id_kelas', '=', 'kelas.id')
            ->select('users.*', 'kelas.nama as nama_kelas')
            ->where('users.id', Auth::id())
            ->firstOrFail();
    }

    /**
     * Ujian jenis=1 wajib didistribusikan ke kelas siswa.
     */
    private function findDistributedExam(int $id): Soal {
        $user = Auth::user();

        if (empty($user->id_kelas)) {
            abort(404);
        }

        return Soal::query()
            ->whereKey($id)
            ->where('jenis', '1')
            ->whereHas('distribusisoals', function ($query) use ($user) {
                $query->where('id_kelas', (string) $user->id_kelas);
            })
            ->firstOrFail();
    }

    /**
     * Latihan jenis=2 wajib terkait materi aktif.
     */
    private function findTraining(int $id): Soal {
        return Soal::query()
            ->whereKey($id)
            ->where('jenis', '2')
            ->whereHas('materiData', function ($query) {
                $query->where('status', 'Y');
            })
            ->firstOrFail();
    }

    /**
     * Resolver paket untuk endpoint engine bersama.
     */
    private function findAccessiblePackage(int $id): Soal {
        $soal = Soal::query()->select('id', 'jenis')->whereKey($id)->firstOrFail();

        return match ((string) $soal->jenis) {
            '1' => $this->findDistributedExam($id),
            '2' => $this->findTraining($id),
            default => abort(404),
        };
    }

    /**
     * Label assessment berdasarkan jenis paket.
     */
    private function assessmentLabel(Soal $soal): string {
        return (string) $soal->jenis === '2' ? 'Latihan' : 'Ujian';
    }

    /**
     * Apakah paket sudah final untuk user ini?
     */
    private function isExamFinished(int $idSoal): bool {
        return Jawab::query()
            ->where('id_soal', (string) $idSoal)
            ->where('id_user', (string) Auth::id())
            ->where('status', 'Y')
            ->exists();
    }

    /**
     * Sisa waktu tanpa mengubah DB.
     */
    private function previewRemainingSeconds(Countexamtime $counter): int {
        $remaining = max(0, (int) $counter->waktu);

        if (!$counter->updated_at) {
            return $remaining;
        }

        $elapsed = max(0, now()->timestamp - $counter->updated_at->timestamp);

        return max(0, $remaining - $elapsed);
    }

    /**
     * Sinkronisasi sisa waktu ke DB.
     */
    private function refreshCounter(Countexamtime $counter): int {
        $remaining = $this->previewRemainingSeconds($counter);

        $counter->waktu = (string) $remaining;
        $counter->save();

        return $remaining;
    }

    /**
     * Finalisasi seluruh soal aktif.
     * Soal yang tidak dijawab tetap dibuat score=0.
     */
    private function finalizeExamRecords(Soal $soal): void {
        $user = Auth::user();

        $details = Detailsoal::query()->where('id_soal', (string) $soal->id)->where('status', 'Y')->get();

        foreach ($details as $detail) {
            $jawab = Jawab::query()->firstOrNew([
                'no_soal_id' => (string) $detail->id,
                'id_soal' => (string) $soal->id,
                'id_user' => (string) $user->id,
            ]);

            $pilihan = strtoupper(trim((string) $jawab->pilihan));

            $kunci = strtoupper(trim((string) $detail->kunci));

            $jawab->id_kelas = (string) $user->id_kelas;
            $jawab->nama = $user->nama;
            $jawab->pilihan = $pilihan;

            $jawab->score = $pilihan !== '' && $pilihan === $kunci ? (string) $detail->score : '0';

            $jawab->status = 'Y';
            $jawab->save();
        }
    }

    /**
     * Key session untuk mempertahankan urutan random soal.
     */
    private function examOrderSessionKey(int $idSoal): string {
        return 'exam_order.' . Auth::id() . '.' . $idSoal;
    }

    /**
     * Validasi urutan soal di session.
     */

    /**
     * Validasi urutan soal di session.
     *
     * @param list<int> $available
     */
    private function isQuestionOrderValid(mixed $order, array $available): bool {
        if (!is_array($order)) {
            return false;
        }

        if (count($order) !== count($available)) {
            return false;
        }

        $orderCopy = array_map('intval', $order);
        $availableCopy = array_map('intval', $available);

        sort($orderCopy);
        sort($availableCopy);

        return $orderCopy === $availableCopy;
    }
}
