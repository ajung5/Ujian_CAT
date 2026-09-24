<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('countexamtimes', function (Blueprint $table) {
            /*
             * Legacy:
             *
             * Satu siswa hanya boleh mempunyai satu timer
             * untuk satu paket soal.
             *
             * Ini tidak kompatibel dengan multi-attempt.
             */
            $table->dropUnique('uq_countexamtimes_soal_user');

            /*
             * Sekarang setiap attempt mempunyai tepat
             * satu timer sendiri.
             */
            $table->unique('attempt_id', 'uq_countexamtimes_attempt');
        });
    }

    public function down(): void {
        Schema::table('countexamtimes', function (Blueprint $table) {
            $table->dropUnique('uq_countexamtimes_attempt');

            $table->unique(['id_soal', 'id_user'], 'uq_countexamtimes_soal_user');
        });
    }
};
