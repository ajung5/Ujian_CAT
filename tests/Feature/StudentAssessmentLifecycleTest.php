<?php

use App\Models\AssessmentAttempt;
use App\Models\Countexamtime;
use App\Models\Detailsoal;
use App\Models\Distribusisoal;
use App\Models\Jawab;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function createLifecycleExam(int $ownerId, Kelas $kelas, array $attributes = []): Soal {
    $soal = Soal::query()->create(
        array_merge(
            [
                'id_user' => (string) $ownerId,
                'jenis' => '1',
                'materi' => null,
                'paket' => 'Ujian Lifecycle Test',
                'deskripsi' => 'Regression test lifecycle ujian',
                'kkm' => '75',
                'waktu' => '3600',
                'tampil' => 'Y',
            ],
            $attributes,
        ),
    );

    Distribusisoal::query()->create([
        'id_soal' => (string) $soal->id,
        'id_kelas' => (string) $kelas->id,
    ]);

    return $soal;
}

function createLifecycleTraining(int $ownerId, array $attributes = []): Soal {
    $materi = Materi::query()->create([
        'id_user' => $ownerId,
        'judul' => 'Materi Lifecycle Test',
        'isi' => 'Materi untuk regression test latihan.',
        'status' => 'Y',
        'hits' => 0,
        'sesi' => 'lifecycle-training-' . uniqid(),
    ]);

    return Soal::query()->create(
        array_merge(
            [
                'id_user' => (string) $ownerId,
                'jenis' => '2',
                'materi' => $materi->id,
                'paket' => 'Latihan Lifecycle Test',
                'deskripsi' => 'Regression test lifecycle latihan',
                'kkm' => '75',
                'waktu' => '1800',
                'tampil' => 'Y',
            ],
            $attributes,
        ),
    );
}

function createLifecycleQuestion(Soal $soal, int $ownerId, array $attributes = []): Detailsoal {
    return Detailsoal::query()->create(
        array_merge(
            [
                'id_soal' => (string) $soal->id,
                'jenis' => (string) $soal->jenis,
                'soal' => 'Pertanyaan lifecycle test',
                'audio' => null,
                'pila' => 'Jawaban A',
                'pilb' => 'Jawaban B',
                'pilc' => 'Jawaban C',
                'pild' => 'Jawaban D',
                'pile' => 'Jawaban E',
                'kunci' => 'A',
                'score' => '100',
                'id_user' => (string) $ownerId,
                'status' => 'Y',
                'sesi' => null,
            ],
            $attributes,
        ),
    );
}

function createLifecycleAttempt(Soal $soal, User $student, array $attributes = []): AssessmentAttempt {
    $status = (string) ($attributes['status'] ?? AssessmentAttempt::STATUS_FINISHED);

    return AssessmentAttempt::query()->create(
        array_merge(
            [
                'id_soal' => $soal->id,
                'id_user' => $student->id,
                'attempt_no' => 1,
                'status' => $status,
                'score' => $status === AssessmentAttempt::STATUS_FINISHED ? 100 : null,
                'started_at' => now()->subMinute(),
                'finished_at' => $status === AssessmentAttempt::STATUS_FINISHED ? now() : null,
            ],
            $attributes,
        ),
    );
}

function createLifecycleStudentAnswer(Soal $soal, Detailsoal $detail, User $student, array $attributes = []): Jawab {
    $attemptId = $attributes['attempt_id'] ?? null;

    if ($attemptId === null) {
        $answerStatus = (string) ($attributes['status'] ?? 'Y');

        $attemptStatus =
            $answerStatus === 'Y' ? AssessmentAttempt::STATUS_FINISHED : AssessmentAttempt::STATUS_IN_PROGRESS;

        $attempt = AssessmentAttempt::query()
            ->where('id_soal', $soal->id)
            ->where('id_user', $student->id)
            ->where('attempt_no', 1)
            ->first();

        if (!$attempt) {
            $attempt = createLifecycleAttempt($soal, $student, [
                'status' => $attemptStatus,
                'score' =>
                    $attemptStatus === AssessmentAttempt::STATUS_FINISHED
                        ? (float) ($attributes['score'] ?? 100)
                        : null,
                'finished_at' => $attemptStatus === AssessmentAttempt::STATUS_FINISHED ? now() : null,
            ]);
        }

        $attemptId = $attempt->id;
    }

    return Jawab::query()->create(
        array_merge(
            [
                'attempt_id' => $attemptId,
                'no_soal_id' => $detail->id,
                'id_soal' => (string) $soal->id,
                'id_user' => (string) $student->id,
                'id_kelas' => $student->id_kelas !== null ? (string) $student->id_kelas : null,
                'nama' => $student->nama,
                'pilihan' => 'A',
                'score' => '100',
                'status' => 'Y',
            ],
            $attributes,
        ),
    );
}

/*
|--------------------------------------------------------------------------
| Start Assessment
|--------------------------------------------------------------------------
*/

test('siswa tidak dapat memulai ujian yang tidak didistribusikan ke kelasnya', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Lifecycle',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $ujian = Soal::query()->create([
        'id_user' => (string) $guru->id,
        'jenis' => '1',
        'materi' => null,
        'paket' => 'Ujian Tidak Terdistribusi',
        'deskripsi' => 'Tidak boleh dimulai',
        'kkm' => '75',
        'waktu' => '3600',
        'tampil' => 'Y',
    ]);

    createLifecycleQuestion($ujian, $guru->id);

    $response = $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujian->id));

    $response->assertNotFound();

    $this->assertDatabaseMissing('countexamtimes', [
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
    ]);

    $this->assertDatabaseMissing('assessment_attempts', [
        'id_soal' => $ujian->id,
        'id_user' => $siswa->id,
    ]);
});

/*
|--------------------------------------------------------------------------
| Answer Before Start
|--------------------------------------------------------------------------
*/

test('siswa tidak dapat menyimpan jawaban sebelum ujian dimulai', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Before Start',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $ujian = createLifecycleExam($guru->id, $kelas);

    $detail = createLifecycleQuestion($ujian, $guru->id);

    $this->actingAs($siswa)->get(route('siswa.exam', $ujian->id))->assertOk();

    $response = $this->actingAs($siswa)->postJson(route('siswa.exam.answer'), [
        'id_soal' => $ujian->id,
        'no_soal_id' => $detail->id,
        'pilihan' => 'A',
    ]);

    $response->assertStatus(409)->assertJson([
        'message' => 'Ujian belum dimulai.',
    ]);

    $this->assertDatabaseMissing('jawabs', [
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
    ]);
});

/*
|--------------------------------------------------------------------------
| Full Lifecycle
|--------------------------------------------------------------------------
*/

test('alur ujian start simpan jawaban dan finish berjalan sampai final', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Full Lifecycle',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
        'nama' => 'Siswa Lifecycle',
    ]);

    $ujian = createLifecycleExam($guru->id, $kelas);

    $detail = createLifecycleQuestion($ujian, $guru->id, [
        'kunci' => 'A',
        'score' => '100',
    ]);

    $this->actingAs($siswa)->get(route('siswa.exam', $ujian->id))->assertOk();

    $startResponse = $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujian->id));

    $startResponse->assertOk()->assertJson([
        'started' => true,
        'attempt_no' => 1,
    ]);

    $attempt = AssessmentAttempt::query()->where('id_soal', $ujian->id)->where('id_user', $siswa->id)->firstOrFail();

    expect($attempt->attempt_no)->toBe(1);

    expect($attempt->status)->toBe(AssessmentAttempt::STATUS_IN_PROGRESS);

    $this->assertDatabaseHas('countexamtimes', [
        'attempt_id' => $attempt->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'waktu' => '3600',
    ]);

    $answerResponse = $this->actingAs($siswa)->postJson(route('siswa.exam.answer'), [
        'id_soal' => $ujian->id,
        'no_soal_id' => $detail->id,
        'pilihan' => 'A',
    ]);

    $answerResponse->assertOk()->assertJson([
        'saved' => true,
        'pilihan' => 'A',
    ]);

    $this->assertDatabaseHas('jawabs', [
        'attempt_id' => $attempt->id,
        'no_soal_id' => $detail->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'id_kelas' => (string) $kelas->id,
        'pilihan' => 'A',
        'score' => '100',
        'status' => 'N',
    ]);

    $finishResponse = $this->actingAs($siswa)->postJson(route('siswa.exam.finish'), [
        'id_soal' => $ujian->id,
    ]);

    $finishResponse->assertOk()->assertJson([
        'finished' => true,
        'attempt_no' => 1,
    ]);

    $this->assertDatabaseHas('jawabs', [
        'attempt_id' => $attempt->id,
        'no_soal_id' => $detail->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'pilihan' => 'A',
        'score' => '100',
        'status' => 'Y',
    ]);

    $this->assertDatabaseHas('countexamtimes', [
        'attempt_id' => $attempt->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'waktu' => '0',
    ]);

    $attempt->refresh();

    expect($attempt->status)->toBe(AssessmentAttempt::STATUS_FINISHED);

    expect((float) $attempt->score)->toBe(100.0);

    expect($attempt->finished_at)->not->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Unanswered Question
|--------------------------------------------------------------------------
*/

test('finish membuat jawaban final score nol untuk soal yang tidak dijawab', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Unanswered',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $ujian = createLifecycleExam($guru->id, $kelas);

    $detailDijawab = createLifecycleQuestion($ujian, $guru->id, [
        'soal' => 'Soal yang dijawab',
        'sesi' => 'answered',
    ]);

    $detailKosong = createLifecycleQuestion($ujian, $guru->id, [
        'soal' => 'Soal yang tidak dijawab',
        'sesi' => 'unanswered',
    ]);

    $this->actingAs($siswa)->get(route('siswa.exam', $ujian->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujian->id))->assertOk();

    $attempt = AssessmentAttempt::query()->where('id_soal', $ujian->id)->where('id_user', $siswa->id)->firstOrFail();

    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.answer'), [
            'id_soal' => $ujian->id,
            'no_soal_id' => $detailDijawab->id,
            'pilihan' => 'A',
        ])
        ->assertOk();

    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.finish'), [
            'id_soal' => $ujian->id,
        ])
        ->assertOk();

    $this->assertDatabaseHas('jawabs', [
        'attempt_id' => $attempt->id,
        'no_soal_id' => $detailDijawab->id,
        'id_user' => (string) $siswa->id,
        'score' => '100',
        'status' => 'Y',
    ]);

    $this->assertDatabaseHas('jawabs', [
        'attempt_id' => $attempt->id,
        'no_soal_id' => $detailKosong->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'pilihan' => '',
        'score' => '0',
        'status' => 'Y',
    ]);
});

/*
|--------------------------------------------------------------------------
| Final Assessment Protection
|--------------------------------------------------------------------------
*/

test('ujian yang sudah final tidak dapat dimulai atau dijawab ulang', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Final Protection',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $ujian = createLifecycleExam($guru->id, $kelas);

    $detail = createLifecycleQuestion($ujian, $guru->id);

    $this->actingAs($siswa)->get(route('siswa.exam', $ujian->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujian->id))->assertOk();

    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.answer'), [
            'id_soal' => $ujian->id,
            'no_soal_id' => $detail->id,
            'pilihan' => 'A',
        ])
        ->assertOk();

    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.finish'), [
            'id_soal' => $ujian->id,
        ])
        ->assertOk();

    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.start', $ujian->id))
        ->assertStatus(409)
        ->assertJson([
            'finished' => true,
            'message' => 'Ujian sudah selesai.',
        ]);

    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.answer'), [
            'id_soal' => $ujian->id,
            'no_soal_id' => $detail->id,
            'pilihan' => 'B',
        ])
        ->assertStatus(409)
        ->assertJson([
            'message' => 'Ujian sudah selesai.',
        ]);

    expect(AssessmentAttempt::query()->where('id_soal', $ujian->id)->where('id_user', $siswa->id)->count())->toBe(1);

    $this->assertDatabaseHas('jawabs', [
        'no_soal_id' => $detail->id,
        'id_user' => (string) $siswa->id,
        'pilihan' => 'A',
        'score' => '100',
        'status' => 'Y',
    ]);
});

/*
|--------------------------------------------------------------------------
| Cross Package Question IDOR
|--------------------------------------------------------------------------
*/

test('siswa tidak dapat mengirim jawaban untuk detail soal milik paket lain', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Cross Package',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $ujianA = createLifecycleExam($guru->id, $kelas, [
        'paket' => 'Paket A',
    ]);

    $ujianB = createLifecycleExam($guru->id, $kelas, [
        'paket' => 'Paket B',
    ]);

    createLifecycleQuestion($ujianA, $guru->id, [
        'soal' => 'Soal Paket A',
    ]);

    $detailB = createLifecycleQuestion($ujianB, $guru->id, [
        'soal' => 'Soal Paket B',
    ]);

    $this->actingAs($siswa)->get(route('siswa.exam', $ujianA->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujianA->id))->assertOk();

    $response = $this->actingAs($siswa)->postJson(route('siswa.exam.answer'), [
        'id_soal' => $ujianA->id,
        'no_soal_id' => $detailB->id,
        'pilihan' => 'A',
    ]);

    $response->assertNotFound();

    $this->assertDatabaseMissing('jawabs', [
        'no_soal_id' => $detailB->id,
        'id_user' => (string) $siswa->id,
    ]);
});

/*
|--------------------------------------------------------------------------
| Review Access Control
|--------------------------------------------------------------------------
*/

test('siswa tidak dapat mereview jawaban tipe ujian melalui direct url', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Review Ujian',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
        'nama' => 'Siswa Review Ujian',
    ]);

    $ujian = createLifecycleExam($guru->id, $kelas, [
        'paket' => 'Ujian Tanpa Review',
    ]);

    $detail = createLifecycleQuestion($ujian, $guru->id);

    createLifecycleStudentAnswer($ujian, $detail, $siswa);

    $response = $this->actingAs($siswa)->get(
        route('siswa.results.detail', [
            'id' => $ujian->id,
        ]),
    );

    $response
        ->assertRedirect(route('siswa.results'))
        ->assertSessionHas('error', 'Review jawaban hanya tersedia untuk tipe Latihan.');
});

test('siswa dapat mereview latihan yang sudah final', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Review Latihan',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
        'nama' => 'Siswa Review Latihan',
    ]);

    $latihan = createLifecycleTraining($guru->id, [
        'paket' => 'Latihan Review Test',
    ]);

    $detail = createLifecycleQuestion($latihan, $guru->id, [
        'jenis' => '2',
        'soal' => 'Pertanyaan latihan yang dapat direview',
        'kunci' => 'A',
        'score' => '100',
    ]);

    createLifecycleStudentAnswer($latihan, $detail, $siswa, [
        'pilihan' => 'A',
        'score' => '100',
        'status' => 'Y',
    ]);

    $response = $this->actingAs($siswa)->get(
        route('siswa.results.detail', [
            'id' => $latihan->id,
            'attempt' => 1,
        ]),
    );

    $response
        ->assertOk()
        ->assertSee('Latihan Review Test')
        ->assertSee('Pertanyaan latihan yang dapat direview')
        ->assertSee('Review Jawaban Latihan')
        ->assertSee('1')
        ->assertSee('Kunci Jawaban');
});

test('siswa tidak dapat mereview latihan yang belum final', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Review Draft',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
        'nama' => 'Siswa Review Draft',
    ]);

    $latihan = createLifecycleTraining($guru->id, [
        'paket' => 'Latihan Belum Final',
    ]);

    $detail = createLifecycleQuestion($latihan, $guru->id, [
        'jenis' => '2',
    ]);

    createLifecycleStudentAnswer($latihan, $detail, $siswa, [
        'status' => 'N',
    ]);

    $response = $this->actingAs($siswa)->get(
        route('siswa.results.detail', [
            'id' => $latihan->id,
        ]),
    );

    $response
        ->assertRedirect(route('siswa.results'))
        ->assertSessionHas('error', 'Review jawaban hanya tersedia setelah pengerjaan selesai.');
});

test('halaman hasil menampilkan review melalui riwayat percobaan latihan', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Review Visibility',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
        'nama' => 'Siswa Review Visibility',
    ]);

    $ujian = createLifecycleExam($guru->id, $kelas, [
        'paket' => 'Ujian Tanpa Tombol Review',
    ]);

    $detailUjian = createLifecycleQuestion($ujian, $guru->id, [
        'soal' => 'Pertanyaan ujian final',
    ]);

    createLifecycleStudentAnswer($ujian, $detailUjian, $siswa);

    $latihan = createLifecycleTraining($guru->id, [
        'paket' => 'Latihan Dengan Tombol Review',
    ]);

    $detailLatihan = createLifecycleQuestion($latihan, $guru->id, [
        'jenis' => '2',
        'soal' => 'Pertanyaan latihan final',
    ]);

    createLifecycleStudentAnswer($latihan, $detailLatihan, $siswa);

    $response = $this->actingAs($siswa)->get(route('siswa.results'));

    $response
        ->assertOk()
        ->assertSee('Ujian Tanpa Tombol Review')
        ->assertSee('Latihan Dengan Tombol Review')
        ->assertSee('Riwayat')
        ->assertSee('Riwayat Percobaan')
        ->assertSee('Review')
        ->assertSee('Ulangi');

    $html = $response->getContent();

    $trainingReviewUrl = route('siswa.results.detail', [
        'id' => $latihan->id,
        'attempt' => 1,
    ]);

    $examReviewUrl = route('siswa.results.detail', [
        'id' => $ujian->id,
        'attempt' => 1,
    ]);

    expect($html)->toContain($trainingReviewUrl);

    expect(substr_count($html, $trainingReviewUrl))->toBe(1);

    expect($html)->not->toContain($examReviewUrl);
});

/*
|--------------------------------------------------------------------------
| Multi Attempt Training
|--------------------------------------------------------------------------
*/

test('latihan dapat dikerjakan tiga kali tanpa menimpa jawaban attempt sebelumnya', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Multi Attempt',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
        'nama' => 'Siswa Multi Attempt',
    ]);

    $latihan = createLifecycleTraining($guru->id, [
        'paket' => 'Latihan Tiga Percobaan',
    ]);

    $detail = createLifecycleQuestion($latihan, $guru->id, [
        'jenis' => '2',
        'kunci' => 'A',
        'score' => '100',
    ]);

    $choices = [
        1 => 'A',
        2 => 'B',
        3 => 'A',
    ];

    foreach ($choices as $attemptNo => $choice) {
        $this->actingAs($siswa)->get(route('siswa.training', $latihan->id))->assertOk();

        $this->actingAs($siswa)
            ->postJson(route('siswa.training.start', $latihan->id))
            ->assertOk()
            ->assertJson([
                'started' => true,
                'attempt_no' => $attemptNo,
            ]);

        $this->actingAs($siswa)
            ->postJson(route('siswa.exam.answer'), [
                'id_soal' => $latihan->id,
                'no_soal_id' => $detail->id,
                'pilihan' => $choice,
            ])
            ->assertOk();

        $this->actingAs($siswa)
            ->postJson(route('siswa.exam.finish'), [
                'id_soal' => $latihan->id,
            ])
            ->assertOk()
            ->assertJson([
                'finished' => true,
                'attempt_no' => $attemptNo,
            ]);
    }

    $attempts = AssessmentAttempt::query()
        ->where('id_soal', $latihan->id)
        ->where('id_user', $siswa->id)
        ->orderBy('attempt_no')
        ->get();

    expect($attempts)->toHaveCount(3);

    expect($attempts->pluck('attempt_no')->all())->toBe([1, 2, 3]);

    expect($attempts->pluck('status')->unique()->all())->toBe([AssessmentAttempt::STATUS_FINISHED]);

    expect($attempts->map(fn(AssessmentAttempt $attempt) => (float) $attempt->score)->all())->toBe([100.0, 0.0, 100.0]);

    $answers = Jawab::query()
        ->where('id_soal', (string) $latihan->id)
        ->where('id_user', (string) $siswa->id)
        ->orderBy('attempt_id')
        ->get();

    expect($answers)->toHaveCount(3);

    expect($answers->pluck('attempt_id')->unique())->toHaveCount(3);

    expect($answers->pluck('pilihan')->all())->toBe(['A', 'B', 'A']);
});

test('attempt keempat latihan ditolak', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Attempt Limit',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $latihan = createLifecycleTraining($guru->id, [
        'paket' => 'Latihan Limit 3',
    ]);

    $detail = createLifecycleQuestion($latihan, $guru->id, [
        'jenis' => '2',
    ]);

    for ($attemptNo = 1; $attemptNo <= 3; $attemptNo++) {
        $attempt = createLifecycleAttempt($latihan, $siswa, [
            'attempt_no' => $attemptNo,
            'status' => AssessmentAttempt::STATUS_FINISHED,
            'score' => 100,
            'started_at' => now()->subMinutes(4 - $attemptNo),
            'finished_at' => now()->subMinutes(3 - $attemptNo),
        ]);

        createLifecycleStudentAnswer($latihan, $detail, $siswa, [
            'attempt_id' => $attempt->id,
            'pilihan' => 'A',
            'score' => '100',
            'status' => 'Y',
        ]);
    }

    $this->actingAs($siswa)
        ->postJson(route('siswa.training.start', $latihan->id))
        ->assertStatus(409)
        ->assertJson([
            'finished' => true,
            'message' => 'Latihan sudah mencapai maksimal 3 percobaan.',
        ]);

    $this->actingAs($siswa)
        ->get(route('siswa.training', $latihan->id))
        ->assertRedirect(route('siswa.results'))
        ->assertSessionHas('error', 'Latihan tersebut sudah mencapai maksimal 3 percobaan.');

    expect(AssessmentAttempt::query()->where('id_soal', $latihan->id)->where('id_user', $siswa->id)->count())->toBe(3);
});

test('review dapat memilih histori attempt latihan tertentu', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Review Attempt',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $latihan = createLifecycleTraining($guru->id, [
        'paket' => 'Latihan Review Histori',
    ]);

    $detail = createLifecycleQuestion($latihan, $guru->id, [
        'jenis' => '2',
        'kunci' => 'A',
    ]);

    $attempt1 = createLifecycleAttempt($latihan, $siswa, [
        'attempt_no' => 1,
        'score' => 100,
    ]);

    createLifecycleStudentAnswer($latihan, $detail, $siswa, [
        'attempt_id' => $attempt1->id,
        'pilihan' => 'A',
        'score' => '100',
    ]);

    $attempt2 = createLifecycleAttempt($latihan, $siswa, [
        'attempt_no' => 2,
        'score' => 0,
    ]);

    createLifecycleStudentAnswer($latihan, $detail, $siswa, [
        'attempt_id' => $attempt2->id,
        'pilihan' => 'B',
        'score' => '0',
    ]);

    $response1 = $this->actingAs($siswa)->get(
        route('siswa.results.detail', [
            'id' => $latihan->id,
            'attempt' => 1,
        ]),
    );

    $response1->assertOk()->assertSee('1')->assertSee('Jawaban Anda &amp; Kunci Jawaban', false);

    $response2 = $this->actingAs($siswa)->get(
        route('siswa.results.detail', [
            'id' => $latihan->id,
            'attempt' => 2,
        ]),
    );

    $response2
        ->assertOk()
        ->assertSee('2')
        ->assertDontSee('Jawaban Anda &amp; Kunci Jawaban', false)
        ->assertSee('label label-danger', false);
});

test('halaman hasil menampilkan histori attempt dan nilai attempt terbaru', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas History Result',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $latihan = createLifecycleTraining($guru->id, [
        'paket' => 'Latihan History Result',
    ]);

    $detail = createLifecycleQuestion($latihan, $guru->id, [
        'jenis' => '2',
    ]);

    $attempt1 = createLifecycleAttempt($latihan, $siswa, [
        'attempt_no' => 1,
        'score' => 40,
        'finished_at' => now()->subMinutes(10),
    ]);

    createLifecycleStudentAnswer($latihan, $detail, $siswa, [
        'attempt_id' => $attempt1->id,
        'pilihan' => 'B',
        'score' => '40',
    ]);

    $attempt2 = createLifecycleAttempt($latihan, $siswa, [
        'attempt_no' => 2,
        'score' => 90,
        'finished_at' => now(),
    ]);

    createLifecycleStudentAnswer($latihan, $detail, $siswa, [
        'attempt_id' => $attempt2->id,
        'pilihan' => 'A',
        'score' => '90',
    ]);

    $response = $this->actingAs($siswa)->get(route('siswa.results'));

    $response
        ->assertOk()
        ->assertSee('Latihan History Result')
        ->assertSee('Percobaan terakhir:')
        ->assertSee('2')
        ->assertSee('90')
        ->assertSee('Riwayat')
        ->assertSee('2/3')
        ->assertSee('Riwayat Percobaan')
        ->assertSee('Review')
        ->assertSee('Ulangi');

    $html = $response->getContent();

    $attempt1Url = route('siswa.results.detail', [
        'id' => $latihan->id,
        'attempt' => 1,
    ]);

    $attempt2Url = route('siswa.results.detail', [
        'id' => $latihan->id,
        'attempt' => 2,
    ]);

    expect($html)->toContain($attempt1Url)->toContain($attempt2Url);

    expect(substr_count($html, $attempt1Url))->toBe(1);

    expect(substr_count($html, $attempt2Url))->toBe(1);
});

test('timer setiap attempt latihan terisolasi', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Timer Attempt',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $latihan = createLifecycleTraining($guru->id, [
        'paket' => 'Latihan Timer Attempt',
        'waktu' => '120',
    ]);

    $detail = createLifecycleQuestion($latihan, $guru->id, [
        'jenis' => '2',
    ]);

    $this->actingAs($siswa)->get(route('siswa.training', $latihan->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.training.start', $latihan->id))->assertOk();

    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.answer'), [
            'id_soal' => $latihan->id,
            'no_soal_id' => $detail->id,
            'pilihan' => 'A',
        ])
        ->assertOk();

    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.finish'), [
            'id_soal' => $latihan->id,
        ])
        ->assertOk();

    $attempt1 = AssessmentAttempt::query()
        ->where('id_soal', $latihan->id)
        ->where('id_user', $siswa->id)
        ->where('attempt_no', 1)
        ->firstOrFail();

    $counter1 = Countexamtime::query()->where('attempt_id', $attempt1->id)->firstOrFail();

    expect((int) $counter1->waktu)->toBe(0);

    $this->actingAs($siswa)->get(route('siswa.training', $latihan->id))->assertOk();

    $this->actingAs($siswa)
        ->postJson(route('siswa.training.start', $latihan->id))
        ->assertOk()
        ->assertJson([
            'attempt_no' => 2,
        ]);

    $attempt2 = AssessmentAttempt::query()
        ->where('id_soal', $latihan->id)
        ->where('id_user', $siswa->id)
        ->where('attempt_no', 2)
        ->firstOrFail();

    $counter2 = Countexamtime::query()->where('attempt_id', $attempt2->id)->firstOrFail();

    expect($attempt2->id)->not->toBe($attempt1->id);

    expect($counter2->id)->not->toBe($counter1->id);

    expect((int) $counter2->waktu)->toBeGreaterThan(0);

    $counter1->refresh();

    expect((int) $counter1->waktu)->toBe(0);
});

/*
|--------------------------------------------------------------------------
| Timer Expiry
|--------------------------------------------------------------------------
*/

test('timer yang habis otomatis memfinalisasi ujian', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Timer Expired',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $ujian = createLifecycleExam($guru->id, $kelas, [
        'waktu' => '10',
    ]);

    $detail = createLifecycleQuestion($ujian, $guru->id);

    $this->actingAs($siswa)->get(route('siswa.exam', $ujian->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujian->id))->assertOk();

    $attempt = AssessmentAttempt::query()->where('id_soal', $ujian->id)->where('id_user', $siswa->id)->firstOrFail();

    DB::table('countexamtimes')
        ->where('attempt_id', $attempt->id)
        ->update([
            'updated_at' => now()->subSeconds(20),
        ]);

    $response = $this->actingAs($siswa)->postJson(route('siswa.exam.time'), [
        'id_soal' => $ujian->id,
    ]);

    $response->assertOk()->assertJson([
        'expired' => true,
        'remaining_seconds' => 0,
    ]);

    $this->assertDatabaseHas('jawabs', [
        'attempt_id' => $attempt->id,
        'no_soal_id' => $detail->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'score' => '0',
        'status' => 'Y',
    ]);

    $this->assertDatabaseHas('countexamtimes', [
        'attempt_id' => $attempt->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'waktu' => '0',
    ]);

    $attempt->refresh();

    expect($attempt->status)->toBe(AssessmentAttempt::STATUS_FINISHED);
});

/*
|--------------------------------------------------------------------------
| Timer Isolation
|--------------------------------------------------------------------------
*/

test('timer satu paket tidak mengubah timer paket lain', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Timer Scope',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $ujianA = createLifecycleExam($guru->id, $kelas, [
        'paket' => 'Timer Paket A',
        'waktu' => '10',
    ]);

    $ujianB = createLifecycleExam($guru->id, $kelas, [
        'paket' => 'Timer Paket B',
        'waktu' => '120',
    ]);

    createLifecycleQuestion($ujianA, $guru->id);

    createLifecycleQuestion($ujianB, $guru->id);

    $this->actingAs($siswa)->get(route('siswa.exam', $ujianA->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujianA->id))->assertOk();

    $this->actingAs($siswa)->get(route('siswa.exam', $ujianB->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujianB->id))->assertOk();

    $attemptA = AssessmentAttempt::query()->where('id_soal', $ujianA->id)->where('id_user', $siswa->id)->firstOrFail();

    $attemptB = AssessmentAttempt::query()->where('id_soal', $ujianB->id)->where('id_user', $siswa->id)->firstOrFail();

    DB::table('countexamtimes')
        ->where('attempt_id', $attemptA->id)
        ->update([
            'updated_at' => now()->subSeconds(20),
        ]);

    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.time'), [
            'id_soal' => $ujianA->id,
        ])
        ->assertOk()
        ->assertJson([
            'expired' => true,
        ]);

    $this->assertDatabaseHas('countexamtimes', [
        'attempt_id' => $attemptA->id,
        'waktu' => '0',
    ]);

    $timerB = Countexamtime::query()->where('attempt_id', $attemptB->id)->firstOrFail();

    expect((int) $timerB->waktu)->toBeGreaterThan(0);
});
