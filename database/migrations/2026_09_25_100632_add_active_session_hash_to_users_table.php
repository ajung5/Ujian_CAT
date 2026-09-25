<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        /*
         * Database legacy menggunakan zero-date default
         * pada users.created_at dan users.updated_at.
         *
         * MySQL modern dengan NO_ZERO_DATE akan menolak
         * ALTER TABLE lain selama definisi tersebut masih ada.
         */
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE `users`
                 MODIFY `created_at`
                 TIMESTAMP NULL DEFAULT NULL',
            );

            DB::statement(
                'ALTER TABLE `users`
                 MODIFY `updated_at`
                 TIMESTAMP NULL DEFAULT NULL',
            );
        }

        if (!Schema::hasColumn('users', 'active_session_hash')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->char('active_session_hash', 64)->nullable()->after('remember_token');
            });
        }
    }

    public function down(): void {
        if (Schema::hasColumn('users', 'active_session_hash')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('active_session_hash');
            });
        }

        /*
         * Timestamp tidak dikembalikan ke zero-date.
         * Format zero-date tidak kompatibel dengan
         * konfigurasi MySQL modern.
         */
    }
};
