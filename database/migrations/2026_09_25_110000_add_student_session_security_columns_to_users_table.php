<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        $hasRevokedAt = Schema::hasColumn('users', 'student_session_revoked_at');
        $hasLastLoginAt = Schema::hasColumn('users', 'last_login_at');
        $hasLastLoginIp = Schema::hasColumn('users', 'last_login_ip');
        $hasLastLoginUserAgent = Schema::hasColumn('users', 'last_login_user_agent');

        Schema::table('users', function (Blueprint $table) use (
            $hasRevokedAt,
            $hasLastLoginAt,
            $hasLastLoginIp,
            $hasLastLoginUserAgent,
        ): void {
            if (!$hasRevokedAt) {
                $table->timestamp('student_session_revoked_at')->nullable()->after('active_session_hash');
            }

            if (!$hasLastLoginAt) {
                $table->timestamp('last_login_at')->nullable()->after('student_session_revoked_at');
            }

            if (!$hasLastLoginIp) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }

            if (!$hasLastLoginUserAgent) {
                $table->text('last_login_user_agent')->nullable()->after('last_login_ip');
            }
        });
    }

    public function down(): void {
        $columns = array_values(
            array_filter(
                ['student_session_revoked_at', 'last_login_at', 'last_login_ip', 'last_login_user_agent'],
                static fn(string $column): bool => Schema::hasColumn('users', $column),
            ),
        );

        if ($columns !== []) {
            Schema::table('users', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }
};
