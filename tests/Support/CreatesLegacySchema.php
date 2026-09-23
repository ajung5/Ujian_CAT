<?php

namespace Tests\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

trait CreatesLegacySchema {
    protected function createLegacySchema(): void {
        $connection = Schema::getConnection();

        if ($connection->getDriverName() !== 'sqlite' || $connection->getDatabaseName() !== ':memory:') {
            throw new RuntimeException(
                'SAFETY ABORT: test schema hanya boleh dijalankan pada SQLite :memory:. ' .
                    'Connection aktif: ' .
                    $connection->getDriverName() .
                    ', database: ' .
                    $connection->getDatabaseName(),
            );
        }
        $tables = [
            'aktifitas',
            'countexamtimes',
            'jawabs',
            'distribusisoals',
            'detailsoals',
            'soals',
            'materis',
            'users',
            'kelas',
            'schools',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nama')->nullable();
            $table->text('alamat')->nullable();
            $table->string('logo')->nullable();
            $table->string('header')->nullable();
            $table->text('motto')->nullable();

            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nama');

            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('id_kelas')->nullable();

            $table->string('nama')->default('');
            $table->string('no_induk')->nullable();
            $table->string('jk', 1)->nullable();
            $table->string('status', 1);

            $table->string('gambar')->default('');

            $table->string('email')->unique();
            $table->string('password');

            $table->rememberToken();

            $table->string('sekolah_asal')->default('');

            $table->timestamps();
        });

        Schema::create('materis', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('id_user');

            $table->string('judul');
            $table->longText('isi');

            $table->string('gambar')->nullable();

            $table->string('status', 1)->default('Y');

            $table->integer('hits')->default(0);

            $table->string('sesi', 32)->nullable();

            $table->timestamps();
        });

        Schema::create('soals', function (Blueprint $table) {
            $table->increments('id');

            $table->string('id_user', 50);

            $table->string('jenis', 1)->default('1');

            $table->unsignedInteger('materi')->nullable();

            $table->string('paket');
            $table->string('deskripsi');

            $table->string('kkm', 5);
            $table->string('waktu', 25);

            $table->string('tampil', 1)->nullable();

            $table->timestamps();
        });

        Schema::create('detailsoals', function (Blueprint $table) {
            $table->increments('id');

            $table->string('id_soal', 150);

            $table->string('jenis', 5);

            $table->longText('soal');

            $table->string('audio')->nullable();

            $table->longText('pila');
            $table->longText('pilb');
            $table->longText('pilc');
            $table->longText('pild');
            $table->longText('pile');

            $table->string('kunci', 1);

            $table->string('score', 50)->nullable();

            $table->string('id_user', 15);

            $table->string('status', 1);

            $table->string('sesi', 32)->nullable();

            $table->timestamps();
        });

        Schema::create('distribusisoals', function (Blueprint $table) {
            $table->increments('id');

            $table->string('id_soal', 15);
            $table->string('id_kelas', 15);

            $table->timestamps();
        });

        Schema::create('jawabs', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('no_soal_id');

            $table->string('id_soal', 150);
            $table->string('id_user', 15);

            $table->string('id_kelas', 15)->nullable();

            $table->string('nama')->nullable();

            $table->string('pilihan', 5)->default('');

            $table->string('score', 50)->nullable();

            $table->string('status', 1);

            $table->timestamps();
        });

        Schema::create('countexamtimes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('id_soal', 150);
            $table->string('id_user', 15);
            $table->string('waktu', 25);

            $table->timestamps();
        });

        Schema::create('aktifitas', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('id_user');
            $table->string('nama');

            $table->timestamps();
        });
    }
}
