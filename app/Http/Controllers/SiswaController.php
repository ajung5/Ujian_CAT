<?php

namespace App\Http\Controllers;

use App\Models\AssessmentAttempt;
use App\Models\Countexamtime;
use App\Models\Detailsoal;
use App\Models\Distribusisoal;
use App\Models\Jawab;
use App\Models\School;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiswaController extends Controller {
    private const MAX_EXAM_ATTEMPTS = 1;

    private const MAX_TRAINING_ATTEMPTS = 3;

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

        $completedExamIds = AssessmentAttempt::query()
            ->join('soals', 'assessment_attempts.id_soal', '=', 'soals.id')
            ->where('assessment_attempts.id_user', Auth::id())
            ->where('assessment_attempts.status', AssessmentAttempt::STATUS_FINISHED)
            ->where('soals.jenis', '1')
            ->pluck('assessment_attempts.id_soal')
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

        $activeAttempt = $this->activeAttempt($soal);
        $completedAttempts = $this->completedAttemptCount($soal);
        $maxAttempts = $this->attemptLimit($soal);

        if (!$activeAttempt && $completedAttempts >= $maxAttempts) {
            $message =
                (string) $soal->jenis === '2'
                    ? 'Latihan tersebut sudah mencapai maksimal 3 percobaan.'
                    : 'Ujian tersebut sudah selesai.';

            return redirect()->route('siswa.results')->with('error', $message);
        }

        $attemptNo = $activeAttempt ? $activeAttempt->attempt_no : $completedAttempts + 1;

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

        $sessionKey = $this->examOrderSessionKey($soal->id, $attemptNo);
        $questionOrder = session($sessionKey);
        $availableArray = $availableIds->all();

        if (!$this->isQuestionOrderValid($questionOrder, $availableArray)) {
            $questionOrder = $availableIds->shuffle()->values()->all();

            session([
                $sessionKey => $questionOrder,
            ]);
        }

        $answeredIds = [];

        if ($activeAttempt) {
            $answeredIds = Jawab::query()
                ->where('attempt_id', $activeAttempt->id)
                ->where('status', 'N')
                ->pluck('no_soal_id')
                ->map(fn($id) => (int) $id)
                ->values()
                ->all();
        }

        $counter = null;

        if ($activeAttempt) {
            $counter = Countexamtime::query()->where('attempt_id', $activeAttempt->id)->first();
        }

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
                'attemptNo',
                'completedAttempts',
                'maxAttempts',
            ),
        );
    }

    /**
     * Mulai / lanjutkan Ujian atau Latihan.
     */
    public function startExam(int $id): JsonResponse {
        $soal = $this->findAccessiblePackage($id);
        $assessmentLabel = $this->assessmentLabel($soal);

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
            $attempt = AssessmentAttempt::query()
                ->where('id_soal', $soal->id)
                ->where('id_user', Auth::id())
                ->where('status', AssessmentAttempt::STATUS_IN_PROGRESS)
                ->lockForUpdate()
                ->first();

            if (!$attempt) {
                $completed = AssessmentAttempt::query()
                    ->where('id_soal', $soal->id)
                    ->where('id_user', Auth::id())
                    ->where('status', AssessmentAttempt::STATUS_FINISHED)
                    ->count();

                if ($completed >= $this->attemptLimit($soal)) {
                    return [
                        'maxed' => true,
                        'attempt' => null,
                        'remaining' => 0,
                        'expired' => false,
                        'score' => null,
                    ];
                }

                $nextAttemptNo =
                    (int) AssessmentAttempt::query()
                        ->where('id_soal', $soal->id)
                        ->where('id_user', Auth::id())
                        ->max('attempt_no') + 1;

                $attempt = AssessmentAttempt::query()->create([
                    'id_soal' => $soal->id,
                    'id_user' => Auth::id(),
                    'attempt_no' => $nextAttemptNo,
                    'status' => AssessmentAttempt::STATUS_IN_PROGRESS,
                    'score' => null,
                    'started_at' => now(),
                    'finished_at' => null,
                ]);
            }

            $counter = Countexamtime::query()->where('attempt_id', $attempt->id)->lockForUpdate()->first();

            if (!$counter) {
                $counter = new Countexamtime();
                $counter->attempt_id = $attempt->id;
                $counter->id_soal = (string) $soal->id;
                $counter->id_user = (string) Auth::id();
                $counter->waktu = (string) max(0, (int) $soal->waktu);
                $counter->save();

                return [
                    'maxed' => false,
                    'attempt' => $attempt,
                    'remaining' => (int) $counter->waktu,
                    'expired' => false,
                    'score' => null,
                ];
            }

            $remaining = $this->refreshCounter($counter);

            if ($remaining <= 0) {
                $score = $this->completeAttempt($soal, $attempt, $counter);

                return [
                    'maxed' => false,
                    'attempt' => $attempt,
                    'remaining' => 0,
                    'expired' => true,
                    'score' => $score,
                ];
            }

            return [
                'maxed' => false,
                'attempt' => $attempt,
                'remaining' => $remaining,
                'expired' => false,
                'score' => null,
            ];
        });

        if ($result['maxed']) {
            $message =
                (string) $soal->jenis === '2' ? 'Latihan sudah mencapai maksimal 3 percobaan.' : 'Ujian sudah selesai.';

            return response()->json(
                [
                    'message' => $message,
                    'finished' => true,
                    'redirect' => route('siswa.results'),
                ],
                409,
            );
        }

        $attempt = $result['attempt'];

        if ($result['expired']) {
            session()->forget($this->examOrderSessionKey($soal->id, $attempt->attempt_no));

            return response()->json(
                [
                    'message' => 'Waktu ' . strtolower($assessmentLabel) . ' telah habis.',
                    'expired' => true,
                    'remaining_seconds' => 0,
                    'attempt_no' => $attempt->attempt_no,
                    'redirect' => route('siswa.results'),
                ],
                409,
            );
        }

        Log::info('assessment.started', [
            'type' => (string) $soal->jenis,
            'user_id' => Auth::id(),
            'id_soal' => $soal->id,
            'attempt_id' => $attempt->id,
            'attempt_no' => $attempt->attempt_no,
            'remaining_seconds' => $result['remaining'],
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'started' => true,
            'attempt_no' => $attempt->attempt_no,
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
        $attempt = $this->activeAttempt($soal);

        if (!$attempt) {
            if ($this->hasReachedAttemptLimit($soal)) {
                abort(409, $assessmentLabel . ' sudah selesai.');
            }

            abort(409, $assessmentLabel . ' belum dimulai.');
        }

        $counter = Countexamtime::query()->where('attempt_id', $attempt->id)->first();

        if (!$counter) {
            abort(409, $assessmentLabel . ' belum dimulai.');
        }

        if ($this->previewRemainingSeconds($counter) <= 0) {
            abort(409, 'Waktu ' . strtolower($assessmentLabel) . ' telah habis.');
        }

        $questionOrder = session($this->examOrderSessionKey($soal->id, $attempt->attempt_no), []);

        if (!in_array((int) $detailsoal->id, array_map('intval', $questionOrder), true)) {
            abort(404);
        }

        $cekJawaban = Jawab::query()
            ->where('attempt_id', $attempt->id)
            ->where('no_soal_id', (string) $detailsoal->id)
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
        $attempt = $this->activeAttempt($soal);

        if (!$attempt) {
            $message = $this->hasReachedAttemptLimit($soal)
                ? $assessmentLabel . ' sudah selesai.'
                : $assessmentLabel . ' belum dimulai.';

            return response()->json(
                [
                    'message' => $message,
                ],
                409,
            );
        }

        $detail = Detailsoal::query()
            ->whereKey($validated['no_soal_id'])
            ->where('id_soal', (string) $soal->id)
            ->where('status', 'Y')
            ->firstOrFail();

        $questionOrder = session($this->examOrderSessionKey($soal->id, $attempt->attempt_no), []);

        if (!in_array((int) $detail->id, array_map('intval', $questionOrder), true)) {
            abort(404);
        }

        $result = DB::transaction(function () use ($soal, $detail, $validated, $attempt) {
            $counter = Countexamtime::query()->where('attempt_id', $attempt->id)->lockForUpdate()->first();

            if (!$counter) {
                return [
                    'not_started' => true,
                    'expired' => false,
                    'saved' => false,
                    'pilihan' => null,
                    'remaining' => 0,
                ];
            }

            $remaining = $this->refreshCounter($counter);

            if ($remaining <= 0) {
                $this->completeAttempt($soal, $attempt, $counter);

                return [
                    'not_started' => false,
                    'expired' => true,
                    'saved' => false,
                    'pilihan' => null,
                    'remaining' => 0,
                ];
            }

            $pilihan = strtoupper($validated['pilihan']);
            $kunci = strtoupper(trim((string) $detail->kunci));
            $score = $pilihan === $kunci ? (string) $detail->score : '0';

            $user = Auth::user();

            Jawab::query()->updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
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
                'not_started' => false,
                'expired' => false,
                'saved' => true,
                'pilihan' => $pilihan,
                'remaining' => $remaining,
            ];
        });

        if ($result['not_started']) {
            return response()->json(
                [
                    'message' => $assessmentLabel . ' belum dimulai.',
                ],
                409,
            );
        }

        if ($result['expired']) {
            session()->forget($this->examOrderSessionKey($soal->id, $attempt->attempt_no));

            return response()->json(
                [
                    'message' => 'Waktu ' . strtolower($assessmentLabel) . ' telah habis.',
                    'expired' => true,
                    'remaining_seconds' => 0,
                    'attempt_no' => $attempt->attempt_no,
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
        $attempt = $this->activeAttempt($soal);

        if (!$attempt) {
            if ($this->hasReachedAttemptLimit($soal)) {
                return response()->json([
                    'finished' => true,
                    'remaining_seconds' => 0,
                    'redirect' => route('siswa.results'),
                ]);
            }

            return response()->json(
                [
                    'message' => $assessmentLabel . ' belum dimulai.',
                ],
                409,
            );
        }

        $result = DB::transaction(function () use ($soal, $attempt) {
            $counter = Countexamtime::query()->where('attempt_id', $attempt->id)->lockForUpdate()->first();

            if (!$counter) {
                return [
                    'not_started' => true,
                    'expired' => false,
                    'remaining' => 0,
                ];
            }

            $remaining = $this->refreshCounter($counter);

            if ($remaining <= 0) {
                $this->completeAttempt($soal, $attempt, $counter);

                return [
                    'not_started' => false,
                    'expired' => true,
                    'remaining' => 0,
                ];
            }

            return [
                'not_started' => false,
                'expired' => false,
                'remaining' => $remaining,
            ];
        });

        if ($result['not_started']) {
            return response()->json(
                [
                    'message' => $assessmentLabel . ' belum dimulai.',
                ],
                409,
            );
        }

        if ($result['expired']) {
            session()->forget($this->examOrderSessionKey($soal->id, $attempt->attempt_no));

            return response()->json([
                'expired' => true,
                'remaining_seconds' => 0,
                'attempt_no' => $attempt->attempt_no,
                'redirect' => route('siswa.results'),
            ]);
        }

        return response()->json([
            'remaining_seconds' => $result['remaining'],
        ]);
    }

    /**
     * Finalisasi seluruh jawaban pada attempt aktif.
     */
    public function finishExam(Request $request): JsonResponse {
        $validated = $request->validate([
            'id_soal' => ['required', 'integer'],
        ]);

        $soal = $this->findAccessiblePackage((int) $validated['id_soal']);
        $assessmentLabel = $this->assessmentLabel($soal);
        $attempt = $this->activeAttempt($soal);

        if (!$attempt) {
            if ($this->hasReachedAttemptLimit($soal)) {
                return response()->json([
                    'finished' => true,
                    'redirect' => route('siswa.results'),
                ]);
            }

            return response()->json(
                [
                    'message' => $assessmentLabel . ' belum dimulai.',
                ],
                409,
            );
        }

        $result = DB::transaction(function () use ($soal, $attempt) {
            $counter = Countexamtime::query()->where('attempt_id', $attempt->id)->lockForUpdate()->first();

            if (!$counter) {
                return [
                    'not_started' => true,
                    'score' => 0.0,
                ];
            }

            $this->refreshCounter($counter);

            return [
                'not_started' => false,
                'score' => $this->completeAttempt($soal, $attempt, $counter),
            ];
        });

        if ($result['not_started']) {
            return response()->json(
                [
                    'message' => $assessmentLabel . ' belum dimulai.',
                ],
                409,
            );
        }

        session()->forget($this->examOrderSessionKey($soal->id, $attempt->attempt_no));

        Log::info('assessment.finished', [
            'type' => (string) $soal->jenis,
            'user_id' => Auth::id(),
            'id_soal' => $soal->id,
            'attempt_id' => $attempt->id,
            'attempt_no' => $attempt->attempt_no,
            'score' => $result['score'],
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'finished' => true,
            'score' => $result['score'],
            'attempt_no' => $attempt->attempt_no,
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

        $soalIds = $results->getCollection()->pluck('id_soal')->map(fn($id) => (int) $id)->values()->all();

        $attemptHistory = $this->studentAttemptHistory($soalIds);

        return view('siswa.hasil', compact('user', 'school', 'results', 'attemptHistory'));
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

        $soalIds = $results->pluck('id_soal')->map(fn($id) => (int) $id)->values()->all();

        $attemptHistory = $this->studentAttemptHistory($soalIds);

        return view('siswa.ajax.get_hasil', compact('results', 'attemptHistory'));
    }

    /**
     * Review detail hasil.
     *
     * Hanya Latihan yang dapat direview.
     * Jika nomor attempt tidak diberikan,
     * gunakan attempt selesai terbaru.
     */
    public function resultDetail(int $id, ?int $attempt = null): View|RedirectResponse {
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

        $attemptQuery = AssessmentAttempt::query()
            ->where('id_soal', $soal->id)
            ->where('id_user', Auth::id())
            ->where('status', AssessmentAttempt::STATUS_FINISHED);

        if ($attempt !== null) {
            $attemptQuery->where('attempt_no', $attempt);
        } else {
            $attemptQuery->orderByDesc('attempt_no');
        }

        $attemptRecord = $attemptQuery->first();

        if (!$attemptRecord) {
            $hasInProgressAttempt = AssessmentAttempt::query()
                ->where('id_soal', $soal->id)
                ->where('id_user', Auth::id())
                ->where('status', AssessmentAttempt::STATUS_IN_PROGRESS)
                ->exists();

            Log::warning('assessment.review.denied', [
                'reason' => $hasInProgressAttempt ? 'attempt_not_finished' : 'attempt_not_found',
                'user_id' => Auth::id(),
                'id_soal' => $soal->id,
                'attempt_no' => $attempt,
                'ip' => request()->ip(),
            ]);

            return redirect()
                ->route('siswa.results')
                ->with(
                    'error',
                    $hasInProgressAttempt
                        ? 'Review jawaban hanya tersedia setelah pengerjaan selesai.'
                        : 'Riwayat percobaan tidak ditemukan.',
                );
        }

        $jawabs = Detailsoal::query()
            ->leftJoin('jawabs', function ($join) use ($soal, $attemptRecord) {
                $join
                    ->on('detailsoals.id', '=', 'jawabs.no_soal_id')
                    ->where('jawabs.attempt_id', '=', $attemptRecord->id)
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
            'attempt_id' => $attemptRecord->id,
            'attempt_no' => $attemptRecord->attempt_no,
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
                'attemptRecord',
            ),
        );
    }

    /**
     * Query hasil finished attempt terbaru per paket.
     *
     * @return Builder<AssessmentAttempt>
     */
    private function studentResultsQuery(?string $search = null) {
        $userId = (int) Auth::id();

        $latestAttemptIds = AssessmentAttempt::query()
            ->selectRaw('MAX(id)')
            ->where('id_user', $userId)
            ->where('status', AssessmentAttempt::STATUS_FINISHED)
            ->groupBy('id_soal');

        $query = AssessmentAttempt::query()
            ->join('soals', 'assessment_attempts.id_soal', '=', 'soals.id')
            ->select(
                'assessment_attempts.id as attempt_id',
                'assessment_attempts.id_soal',
                'assessment_attempts.attempt_no',
                'assessment_attempts.score as total_score',
                'assessment_attempts.finished_at as completed_at',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.jenis as jenis_soal',
            )
            ->where('assessment_attempts.id_user', $userId)
            ->where('assessment_attempts.status', AssessmentAttempt::STATUS_FINISHED)
            ->whereIn('assessment_attempts.id', $latestAttemptIds);

        if ($search !== null && $search !== '') {
            $query->where('soals.paket', 'like', '%' . $search . '%');
        }

        return $query->orderByDesc('assessment_attempts.finished_at');
    }

    /**
     * Histori finished attempt untuk daftar paket tertentu.
     *
     * @param list<int> $soalIds
     * @return Collection<int|string, Collection<int, AssessmentAttempt>>
     */
    private function studentAttemptHistory(array $soalIds): Collection {
        if ($soalIds === []) {
            return collect();
        }

        $attempts = AssessmentAttempt::query()
            ->where('id_user', Auth::id())
            ->whereIn('id_soal', $soalIds)
            ->where('status', AssessmentAttempt::STATUS_FINISHED)
            ->orderBy('id_soal')
            ->orderByDesc('attempt_no')
            ->get();

        return collect($attempts->all())->groupBy(fn(AssessmentAttempt $attempt) => (string) $attempt->id_soal);
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
     * Batas maksimal attempt.
     *
     * Ujian   = 1
     * Latihan = 3
     */
    private function attemptLimit(Soal $soal): int {
        return (string) $soal->jenis === '2' ? self::MAX_TRAINING_ATTEMPTS : self::MAX_EXAM_ATTEMPTS;
    }

    /**
     * Attempt aktif user untuk paket.
     */
    private function activeAttempt(Soal $soal): ?AssessmentAttempt {
        return AssessmentAttempt::query()
            ->where('id_soal', $soal->id)
            ->where('id_user', Auth::id())
            ->where('status', AssessmentAttempt::STATUS_IN_PROGRESS)
            ->orderByDesc('attempt_no')
            ->first();
    }

    /**
     * Jumlah attempt yang sudah selesai.
     */
    private function completedAttemptCount(Soal $soal): int {
        return AssessmentAttempt::query()
            ->where('id_soal', $soal->id)
            ->where('id_user', Auth::id())
            ->where('status', AssessmentAttempt::STATUS_FINISHED)
            ->count();
    }

    /**
     * Apakah batas attempt sudah tercapai.
     */
    private function hasReachedAttemptLimit(Soal $soal): bool {
        return $this->completedAttemptCount($soal) >= $this->attemptLimit($soal);
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
     * Finalisasi seluruh soal aktif pada satu attempt.
     * Soal yang tidak dijawab tetap dibuat score=0.
     */
    private function finalizeExamRecords(Soal $soal, AssessmentAttempt $attempt): void {
        $user = Auth::user();

        $details = Detailsoal::query()->where('id_soal', (string) $soal->id)->where('status', 'Y')->get();

        foreach ($details as $detail) {
            $jawab = Jawab::query()->firstOrNew([
                'attempt_id' => $attempt->id,
                'no_soal_id' => (string) $detail->id,
                'id_soal' => (string) $soal->id,
                'id_user' => (string) $user->id,
            ]);

            $pilihan = strtoupper(trim((string) $jawab->pilihan));
            $kunci = strtoupper(trim((string) $detail->kunci));

            $jawab->attempt_id = $attempt->id;
            $jawab->id_kelas = (string) $user->id_kelas;
            $jawab->nama = $user->nama;
            $jawab->pilihan = $pilihan;
            $jawab->score = $pilihan !== '' && $pilihan === $kunci ? (string) $detail->score : '0';
            $jawab->status = 'Y';
            $jawab->save();
        }
    }

    /**
     * Finalisasi attempt.
     */
    private function completeAttempt(Soal $soal, AssessmentAttempt $attempt, Countexamtime $counter): float {
        if ($attempt->status === AssessmentAttempt::STATUS_FINISHED) {
            return (float) ($attempt->score ?? 0);
        }

        $this->finalizeExamRecords($soal, $attempt);

        $score = (float) Jawab::query()->where('attempt_id', $attempt->id)->where('status', 'Y')->sum('score');

        $counter->waktu = '0';
        $counter->save();

        $attempt->status = AssessmentAttempt::STATUS_FINISHED;
        $attempt->score = $score;
        $attempt->finished_at = now();
        $attempt->save();

        return $score;
    }

    /**
     * Key session urutan random soal per attempt.
     */
    private function examOrderSessionKey(int $idSoal, int $attemptNo): string {
        return 'exam_order.' . Auth::id() . '.' . $idSoal . '.' . $attemptNo;
    }

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
