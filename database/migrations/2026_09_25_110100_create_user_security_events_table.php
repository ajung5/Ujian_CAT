<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('user_security_events')) {
            return;
        }

        Schema::create('user_security_events', function (Blueprint $table): void {
            $table->id();

            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('actor_user_id')->nullable();

            $table->string('email')->nullable();
            $table->string('role', 1)->nullable();
            $table->string('event', 50);

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->char('session_hash', 64)->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['event', 'created_at']);
            $table->index('actor_user_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_security_events');
    }
};
