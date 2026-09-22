<?php

namespace Tests;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesLegacySchema;

abstract class TestCase extends BaseTestCase {
    use CreatesLegacySchema;

    private static int $userSequence = 1;

    protected function setUp(): void {
        parent::setUp();

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
