<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        /*
         * Database legacy dapat memiliki:
         *
         * created_at / updated_at
         * DEFAULT '0000-00-00 00:00:00'
         *
         * MySQL modern dengan strict mode akan menolak
         * ALTER TABLE terhadap tabel seperti itu.
         *
         * Normalisasi dilakukan terlebih dahulu.
         */
        $this->normalizeLegacyTimestamps();

        /*
         * Tabel utama attempt.
         */
        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('id_soal');

            $table->unsignedInteger('id_user');

            $table->unsignedTinyInteger('attempt_no');

            $table->string('status', 20)->default('in_progress');

            $table->decimal('score', 10, 2)->nullable();

            $table->timestamp('started_at')->nullable();

            $table->timestamp('finished_at')->nullable();

            $table->timestamps();

            $table->unique(['id_soal', 'id_user', 'attempt_no'], 'assessment_attempt_unique');

            $table->index(['id_user', 'status']);

            $table->index(['id_soal', 'id_user']);
        });

        /*
         * Relasikan jawaban dengan attempt.
         */
        Schema::table('jawabs', function (Blueprint $table) {
            $table->unsignedBigInteger('attempt_id')->nullable()->after('id')->index();
        });

        /*
         * Relasikan timer dengan attempt.
         */
        Schema::table('countexamtimes', function (Blueprint $table) {
            $table->unsignedBigInteger('attempt_id')->nullable()->after('id')->index();
        });

        /*
         * Migrasi data existing.
         *
         * Semua data lama dianggap
         * sebagai Attempt #1.
         */
        $answerPairs = DB::table('jawabs')->select('id_soal', 'id_user')->distinct()->get();

        $timerPairs = DB::table('countexamtimes')->select('id_soal', 'id_user')->distinct()->get();

        $pairs = $answerPairs->concat($timerPairs)->unique(function ($row) {
            return (string) $row->id_soal . ':' . (string) $row->id_user;
        });

        foreach ($pairs as $pair) {
            $answers = DB::table('jawabs')->where('id_soal', $pair->id_soal)->where('id_user', $pair->id_user)->get();

            $counter = DB::table('countexamtimes')
                ->where('id_soal', $pair->id_soal)
                ->where('id_user', $pair->id_user)
                ->orderByDesc('id')
                ->first();

            /*
             * Kalau masih ada draft N,
             * attempt dianggap masih berjalan.
             */
            $hasDraft = $answers->contains(function ($answer) {
                return (string) $answer->status === 'N';
            });

            $hasFinal = $answers->contains(function ($answer) {
                return (string) $answer->status === 'Y';
            });

            $status = $hasDraft ? 'in_progress' : ($hasFinal ? 'finished' : 'in_progress');

            $score = null;

            if ($status === 'finished') {
                $score = $answers->where('status', 'Y')->sum(function ($answer) {
                    return (float) ($answer->score ?? 0);
                });
            }

            /*
             * Tentukan waktu mulai
             * dari data legacy.
             */
            $startedAt = $answers->pluck('created_at')->filter()->sort()->first();

            if (!$startedAt && $counter) {
                $startedAt = $counter->created_at;
            }

            $finishedAt = null;

            if ($status === 'finished') {
                $finishedAt = $answers->pluck('updated_at')->filter()->sortDesc()->first();
            }

            /*
             * Pastikan attempt lama tetap
             * mempunyai timestamp masuk akal.
             */
            $startedAt = $startedAt ?: now();

            $finishedAt = $status === 'finished' ? ($finishedAt ?: now()) : null;

            $attemptId = DB::table('assessment_attempts')->insertGetId([
                'id_soal' => (int) $pair->id_soal,
                'id_user' => (int) $pair->id_user,
                'attempt_no' => 1,
                'status' => $status,
                'score' => $score,
                'started_at' => $startedAt,
                'finished_at' => $finishedAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
             * Hubungkan seluruh jawaban lama
             * ke Attempt #1.
             */
            DB::table('jawabs')
                ->where('id_soal', $pair->id_soal)
                ->where('id_user', $pair->id_user)
                ->update([
                    'attempt_id' => $attemptId,
                ]);

            /*
             * Hubungkan timer lama
             * ke Attempt #1.
             */
            DB::table('countexamtimes')
                ->where('id_soal', $pair->id_soal)
                ->where('id_user', $pair->id_user)
                ->update([
                    'attempt_id' => $attemptId,
                ]);
        }
    }

    public function down(): void {
        Schema::table('countexamtimes', function (Blueprint $table) {
            $table->dropColumn('attempt_id');
        });

        Schema::table('jawabs', function (Blueprint $table) {
            $table->dropColumn('attempt_id');
        });

        Schema::dropIfExists('assessment_attempts');
    }

    /**
     * Normalisasi timestamp dari database
     * Laravel legacy agar kompatibel dengan
     * MySQL strict mode.
     */
    private function normalizeLegacyTimestamps(): void {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $result = DB::selectOne('SELECT @@SESSION.sql_mode AS sql_mode');

        $originalSqlMode = (string) ($result->sql_mode ?? '');

        /*
         * ALTER legacy timestamp harus
         * dilakukan tanpa strict zero-date
         * validation.
         *
         * Hanya berlaku pada SESSION migration
         * ini, bukan global MySQL.
         */
        $relaxedModes = array_values(
            array_filter(explode(',', $originalSqlMode), function (string $mode) {
                return !in_array(
                    strtoupper(trim($mode)),
                    ['STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES', 'NO_ZERO_DATE', 'NO_ZERO_IN_DATE'],
                    true,
                );
            }),
        );

        $relaxedSqlMode = implode(',', $relaxedModes);

        try {
            DB::statement('SET SESSION sql_mode = ?', [$relaxedSqlMode]);

            /*
             * Laravel legacy timestamp:
             *
             * TIMESTAMP NOT NULL
             * DEFAULT 0000-00-00 00:00:00
             *
             * Diubah menjadi nullable,
             * default NULL.
             */
            DB::statement(
                '
                ALTER TABLE `jawabs`
                    MODIFY `created_at`
                        TIMESTAMP NULL DEFAULT NULL,
                    MODIFY `updated_at`
                        TIMESTAMP NULL DEFAULT NULL
                ',
            );

            DB::statement(
                '
                ALTER TABLE `countexamtimes`
                    MODIFY `created_at`
                        TIMESTAMP NULL DEFAULT NULL,
                    MODIFY `updated_at`
                        TIMESTAMP NULL DEFAULT NULL
                ',
            );

            /*
             * Bersihkan zero-date legacy.
             */
            DB::statement(
                "
                UPDATE `jawabs`
                SET `created_at` = NULL
                WHERE `created_at`
                    = '0000-00-00 00:00:00'
                ",
            );

            DB::statement(
                "
                UPDATE `jawabs`
                SET `updated_at` = NULL
                WHERE `updated_at`
                    = '0000-00-00 00:00:00'
                ",
            );

            DB::statement(
                "
                UPDATE `countexamtimes`
                SET `created_at` = NULL
                WHERE `created_at`
                    = '0000-00-00 00:00:00'
                ",
            );

            DB::statement(
                "
                UPDATE `countexamtimes`
                SET `updated_at` = NULL
                WHERE `updated_at`
                    = '0000-00-00 00:00:00'
                ",
            );
        } finally {
            /*
             * Wajib kembalikan SQL mode
             * seperti sebelum migration.
             */
            DB::statement('SET SESSION sql_mode = ?', [$originalSqlMode]);
        }
    }
};
