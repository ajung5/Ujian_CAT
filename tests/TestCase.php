<?php

namespace Tests;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesLegacySchema;

abstract class TestCase extends BaseTestCase {
    use CreatesLegacySchema;

    private static int $userSequence = 1;

    protected function setUp(): void {
        parent::setUp();

        /*
         * SAFETY:
         * Regression test HARUS menggunakan SQLite in-memory.
         *
         * Jangan pernah membiarkan test menggunakan database
         * development/production dari .env atau config cache.
         */
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.url' => null,
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);

        /*
         * Buang koneksi SQLite yang mungkin sudah pernah dibuat
         * kemudian jadikan SQLite sebagai default connection.
         */
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        /*
         * CreatesLegacySchema mempunyai fail-safe tambahan:
         * hanya boleh berjalan pada SQLite :memory:.
         */
        $this->createLegacySchema();

        $this->createSchool();
    }

    protected function createSchool(array $attributes = []): School {
        return School::query()->create(
            array_merge(
                [
                    'nama' => 'Sekolah Test',

                    'alamat' => 'Alamat Test',

                    'logo' => '',

                    'header' => '',

                    'motto' => 'Testing',
                ],
                $attributes,
            ),
        );
    }

    protected function createUser(array $attributes = []): User {
        $sequence = self::$userSequence++;

        return User::query()->create(
            array_merge(
                [
                    'id_kelas' => null,

                    'nama' => 'User Test ' . $sequence,

                    'no_induk' => 'TEST-' . $sequence,

                    'jk' => 'L',

                    'status' => 'S',

                    'gambar' => '',

                    'email' => 'user' . $sequence . '@example.test',

                    'password' => Hash::make('password123'),

                    'sekolah_asal' => '',
                ],
                $attributes,
            ),
        );
    }
}
