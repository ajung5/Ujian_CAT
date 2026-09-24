<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('jawabs', function (Blueprint $table) {
            /*
             * Legacy constraint:
             *
             * no_soal_id + id_soal + id_user
             *
             * Tidak kompatibel dengan multi-attempt karena
             * siswa boleh menjawab soal yang sama lagi pada
             * attempt berikutnya.
             */
            $table->dropUnique('uq_jawabs_question_package_user');

            /*
             * Satu soal hanya boleh mempunyai satu jawaban
             * dalam satu attempt.
             */
            $table->unique(['attempt_id', 'no_soal_id'], 'uq_jawabs_attempt_question');
        });
    }

    public function down(): void {
        Schema::table('jawabs', function (Blueprint $table) {
            $table->dropUnique('uq_jawabs_attempt_question');

            $table->unique(['no_soal_id', 'id_soal', 'id_user'], 'uq_jawabs_question_package_user');
        });
    }
};
