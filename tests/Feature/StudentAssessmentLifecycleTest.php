<?php

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
        'sesi' => 'lifecycle-training',
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

function createLifecycleStudentAnswer(Soal $soal, Detailsoal $detail, User $student, array $attributes = []): Jawab {
    return Jawab::query()->create(
        array_merge(
            [
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

    /*
     * Paket sengaja dibuat tanpa
     * Distribusisoal.
     */
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

    /*
     * Membuka halaman ujian membuat
     * questionOrder pada session.
     *
     * Tetapi ujian belum dimulai karena
     * endpoint start belum dipanggil.
     */
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

    /*
     * STEP 1:
     * buka halaman ujian agar urutan soal
     * dibuat pada session.
     */
    $this->actingAs($siswa)->get(route('siswa.exam', $ujian->id))->assertOk();

    /*
     * STEP 2:
     * mulai ujian.
     */
    $startResponse = $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujian->id));

    $startResponse->assertOk()->assertJson([
        'started' => true,
    ]);

    $this->assertDatabaseHas('countexamtimes', [
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'waktu' => '3600',
    ]);

    /*
     * STEP 3:
     * simpan jawaban benar.
     */
    $answerResponse = $this->actingAs($siswa)->postJson(route('siswa.exam.answer'), [
        'id_soal' => $ujian->id,
        'no_soal_id' => $detail->id,
        'pilihan' => 'A',
    ]);

    $answerResponse->assertOk()->assertJson([
        'saved' => true,
        'pilihan' => 'A',
    ]);

    /*
     * Sebelum finish masih draft.
     */
    $this->assertDatabaseHas('jawabs', [
        'no_soal_id' => $detail->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'id_kelas' => (string) $kelas->id,
        'pilihan' => 'A',
        'score' => '100',
        'status' => 'N',
    ]);

    /*
     * STEP 4:
     * finalisasi ujian.
     */
    $finishResponse = $this->actingAs($siswa)->postJson(route('siswa.exam.finish'), [
        'id_soal' => $ujian->id,
    ]);

    $finishResponse->assertOk()->assertJson([
        'finished' => true,
    ]);

    /*
     * Jawaban sekarang final.
     */
    $this->assertDatabaseHas('jawabs', [
        'no_soal_id' => $detail->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'pilihan' => 'A',
        'score' => '100',
        'status' => 'Y',
    ]);

    /*
     * Timer harus berhenti.
     */
    $this->assertDatabaseHas('countexamtimes', [
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'waktu' => '0',
    ]);
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

    /*
     * Hanya jawab soal pertama.
     */
    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.answer'), [
            'id_soal' => $ujian->id,
            'no_soal_id' => $detailDijawab->id,
            'pilihan' => 'A',
        ])
        ->assertOk();

    /*
     * Finish walaupun soal kedua
     * tidak dijawab.
     */
    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.finish'), [
            'id_soal' => $ujian->id,
        ])
        ->assertOk();

    /*
     * Jawaban yang dijawab tetap benar.
     */
    $this->assertDatabaseHas('jawabs', [
        'no_soal_id' => $detailDijawab->id,
        'id_user' => (string) $siswa->id,
        'score' => '100',
        'status' => 'Y',
    ]);

    /*
     * Soal yang tidak dijawab tetap
     * mempunyai record final score 0.
     */
    $this->assertDatabaseHas('jawabs', [
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

    /*
     * Tidak boleh start ulang.
     */
    $this->actingAs($siswa)
        ->postJson(route('siswa.exam.start', $ujian->id))
        ->assertStatus(409)
        ->assertJson([
            'finished' => true,
            'message' => 'Ujian sudah selesai.',
        ]);

    /*
     * Tidak boleh ubah jawaban setelah final.
     */
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

    /*
     * Jawaban final tidak berubah.
     */
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

    /*
     * Session + timer hanya untuk Paket A.
     */
    $this->actingAs($siswa)->get(route('siswa.exam', $ujianA->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujianA->id))->assertOk();

    /*
     * Manipulasi request:
     * id_soal = Paket A
     * no_soal_id = detail Paket B
     */
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

    /*
     * Buat jawaban final agar penolakan benar-benar
     * terjadi karena jenis assessment adalah Ujian,
     * bukan karena pengerjaan belum selesai.
     */
    createLifecycleStudentAnswer($ujian, $detail, $siswa);

    $response = $this->actingAs($siswa)->get(route('siswa.results.detail', $ujian->id));

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

    $response = $this->actingAs($siswa)->get(route('siswa.results.detail', $latihan->id));

    $response
        ->assertOk()
        ->assertSee('Latihan Review Test')
        ->assertSee('Pertanyaan latihan yang dapat direview')
        ->assertSee('Latihan')
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

    /*
     * Status N berarti masih draft /
     * assessment belum difinalisasi.
     */
    createLifecycleStudentAnswer($latihan, $detail, $siswa, [
        'status' => 'N',
    ]);

    $response = $this->actingAs($siswa)->get(route('siswa.results.detail', $latihan->id));

    $response
        ->assertRedirect(route('siswa.results'))
        ->assertSessionHas('error', 'Review jawaban hanya tersedia setelah pengerjaan selesai.');
});

test('halaman hasil hanya menampilkan tombol review jawaban untuk latihan', function () {
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

    /*
     * Ujian final.
     */
    $ujian = createLifecycleExam($guru->id, $kelas, [
        'paket' => 'Ujian Tanpa Tombol Review',
    ]);

    $detailUjian = createLifecycleQuestion($ujian, $guru->id, [
        'soal' => 'Pertanyaan ujian final',
    ]);

    createLifecycleStudentAnswer($ujian, $detailUjian, $siswa);

    /*
     * Latihan final.
     */
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
        ->assertSee('Review Jawaban');

    $html = $response->getContent();

    /*
     * URL review Latihan harus ada.
     */
    expect($html)->toContain(route('siswa.results.detail', $latihan->id));

    /*
     * URL review Ujian tidak boleh ada.
     */
    expect($html)->not->toContain(route('siswa.results.detail', $ujian->id));

    /*
     * Karena hanya ada satu Latihan,
     * tombol Review Jawaban juga hanya
     * boleh muncul satu kali.
     */
    expect(substr_count($html, 'Review Jawaban'))->toBe(1);
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

    /*
     * Simulasikan timer terakhir di-update
     * 20 detik lalu.
     *
     * Query Builder digunakan agar
     * updated_at tidak otomatis ditimpa.
     */
    DB::table('countexamtimes')
        ->where('id_soal', (string) $ujian->id)
        ->where('id_user', (string) $siswa->id)
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

    /*
     * Karena waktu habis, soal yang belum
     * dijawab tetap difinalisasi score 0.
     */
    $this->assertDatabaseHas('jawabs', [
        'no_soal_id' => $detail->id,
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'score' => '0',
        'status' => 'Y',
    ]);

    $this->assertDatabaseHas('countexamtimes', [
        'id_soal' => (string) $ujian->id,
        'id_user' => (string) $siswa->id,
        'waktu' => '0',
    ]);
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

    /*
     * Start Paket A.
     */
    $this->actingAs($siswa)->get(route('siswa.exam', $ujianA->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujianA->id))->assertOk();

    /*
     * Start Paket B.
     */
    $this->actingAs($siswa)->get(route('siswa.exam', $ujianB->id))->assertOk();

    $this->actingAs($siswa)->postJson(route('siswa.exam.start', $ujianB->id))->assertOk();

    /*
     * Paksa Paket A expired.
     */
    DB::table('countexamtimes')
        ->where('id_soal', (string) $ujianA->id)
        ->where('id_user', (string) $siswa->id)
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

    /*
     * Paket A menjadi 0.
     */
    $this->assertDatabaseHas('countexamtimes', [
        'id_soal' => (string) $ujianA->id,
        'id_user' => (string) $siswa->id,
        'waktu' => '0',
    ]);

    /*
     * Timer Paket B tidak boleh ikut
     * berubah menjadi 0.
     */
    $timerB = Countexamtime::query()->where('id_soal', $ujianB->id)->where('id_user', $siswa->id)->firstOrFail();

    expect((int) $timerB->waktu)->toBeGreaterThan(0);
});
