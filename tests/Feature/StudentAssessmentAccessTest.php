<?php

use App\Models\Detailsoal;
use App\Models\Distribusisoal;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Soal;

function createActiveQuestion(Soal $soal, int $userId): Detailsoal {
    return Detailsoal::query()->create([
        'id_soal' => (string) $soal->id,

        'jenis' => '1',

        'soal' => 'Pertanyaan test',

        'audio' => null,

        'pila' => 'Jawaban A',

        'pilb' => 'Jawaban B',

        'pilc' => 'Jawaban C',

        'pild' => 'Jawaban D',

        'pile' => 'Jawaban E',

        'kunci' => 'A',

        'score' => '100',

        'id_user' => (string) $userId,

        'status' => 'Y',

        'sesi' => 'test-session',
    ]);
}

test('siswa hanya dapat membuka ujian yang didistribusikan ke kelasnya', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas A',
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

        'paket' => 'Ujian Test',

        'deskripsi' => 'Ujian regression',

        'kkm' => '75',

        'waktu' => '3600',

        'tampil' => 'Y',
    ]);

    createActiveQuestion($ujian, $guru->id);

    /*
     * Belum didistribusikan.
     */
    $this->actingAs($siswa)->get(route('siswa.exam', $ujian->id))->assertNotFound();

    Distribusisoal::query()->create([
        'id_soal' => (string) $ujian->id,

        'id_kelas' => (string) $kelas->id,
    ]);

    /*
     * Setelah didistribusikan.
     */
    $this->actingAs($siswa)->get(route('siswa.exam', $ujian->id))->assertOk();
});

test('paket latihan tidak dapat dibuka melalui endpoint ujian', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas A',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $materi = Materi::query()->create([
        'id_user' => $guru->id,

        'judul' => 'Materi Aktif',

        'isi' => 'Isi materi',

        'status' => 'Y',

        'hits' => 0,

        'sesi' => 'materi-test',
    ]);

    $latihan = Soal::query()->create([
        'id_user' => (string) $guru->id,

        'jenis' => '2',

        'materi' => $materi->id,

        'paket' => 'Latihan Test',

        'deskripsi' => 'Latihan regression',

        'kkm' => '75',

        'waktu' => '1800',

        'tampil' => 'Y',
    ]);

    createActiveQuestion($latihan, $guru->id);

    $this->actingAs($siswa)->get(route('siswa.exam', $latihan->id))->assertNotFound();
});

test('paket ujian tidak dapat dibuka melalui endpoint latihan', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas A',
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

        'paket' => 'Ujian Test',

        'deskripsi' => 'Ujian regression',

        'kkm' => '75',

        'waktu' => '3600',

        'tampil' => 'Y',
    ]);

    createActiveQuestion($ujian, $guru->id);

    $this->actingAs($siswa)->get(route('siswa.training', $ujian->id))->assertNotFound();
});

test('latihan dengan materi aktif dapat dibuka siswa', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas A',
    ]);

    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $materi = Materi::query()->create([
        'id_user' => $guru->id,

        'judul' => 'Materi Aktif',

        'isi' => 'Isi materi test',

        'status' => 'Y',

        'hits' => 0,

        'sesi' => 'materi-aktif',
    ]);

    $latihan = Soal::query()->create([
        'id_user' => (string) $guru->id,

        'jenis' => '2',

        'materi' => $materi->id,

        'paket' => 'Latihan Aktif',

        'deskripsi' => 'Latihan test',

        'kkm' => '75',

        'waktu' => '1800',

        'tampil' => 'Y',
    ]);

    createActiveQuestion($latihan, $guru->id);

    $this->actingAs($siswa)->get(route('siswa.training', $latihan->id))->assertOk()->assertSee('Latihan Aktif');
});

test('latihan dengan materi nonaktif ditolak', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
    ]);

    $materi = Materi::query()->create([
        'id_user' => $guru->id,

        'judul' => 'Materi Nonaktif',

        'isi' => 'Tidak boleh dibuka',

        'status' => 'N',

        'hits' => 0,

        'sesi' => 'materi-nonaktif',
    ]);

    $latihan = Soal::query()->create([
        'id_user' => (string) $guru->id,

        'jenis' => '2',

        'materi' => $materi->id,

        'paket' => 'Latihan Nonaktif',

        'deskripsi' => 'Tidak aktif',

        'kkm' => '75',

        'waktu' => '1800',

        'tampil' => 'Y',
    ]);

    createActiveQuestion($latihan, $guru->id);

    $this->actingAs($siswa)->get(route('siswa.training', $latihan->id))->assertNotFound();
});
