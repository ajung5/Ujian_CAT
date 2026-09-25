<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSecurityEvent;
use Illuminate\Http\Request;

class SecurityEventLogger {
    /**
     * @param array<string, mixed> $metadata
     */
    public function log(
        Request $request,
        string $event,
        ?User $subject = null,
        ?User $actor = null,
        array $metadata = [],
        ?string $email = null,
    ): UserSecurityEvent {
        $studentSessionToken = (string) $request->session()->get('student_session_token', '');

        return UserSecurityEvent::query()->create([
            'user_id' => $subject?->id,
            'actor_user_id' => $actor?->id,
            'email' => $email ?? $subject?->email,
            'role' => $subject?->status,
            'event' => $event,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_hash' => $studentSessionToken !== '' ? hash('sha256', $studentSessionToken) : null,
            'metadata' => $metadata === [] ? null : $metadata,
            'created_at' => now(),
        ]);
    }
}
