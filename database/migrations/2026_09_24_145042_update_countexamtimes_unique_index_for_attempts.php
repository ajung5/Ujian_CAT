<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if ($this->indexExists('countexamtimes', 'uq_countexamtimes_soal_user')) {
            Schema::table('countexamtimes', function (Blueprint $table) {
                $table->dropUnique('uq_countexamtimes_soal_user');
            });
        }

        if (!$this->indexExists('countexamtimes', 'uq_countexamtimes_attempt')) {
            Schema::table('countexamtimes', function (Blueprint $table) {
                $table->unique('attempt_id', 'uq_countexamtimes_attempt');
            });
        }
    }

    public function down(): void {
        if ($this->indexExists('countexamtimes', 'uq_countexamtimes_attempt')) {
            Schema::table('countexamtimes', function (Blueprint $table) {
                $table->dropUnique('uq_countexamtimes_attempt');
            });
        }

        if (!$this->indexExists('countexamtimes', 'uq_countexamtimes_soal_user')) {
            Schema::table('countexamtimes', function (Blueprint $table) {
                $table->unique(['id_soal', 'id_user'], 'uq_countexamtimes_soal_user');
            });
        }
    }

    private function indexExists(string $table, string $index): bool {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return false;
        }

        $database = DB::connection()->getDatabaseName();

        $result = DB::selectOne(
            '
            SELECT COUNT(*) AS aggregate
            FROM information_schema.statistics
            WHERE table_schema = ?
              AND table_name = ?
              AND index_name = ?
            ',
            [$database, $table, $index],
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
