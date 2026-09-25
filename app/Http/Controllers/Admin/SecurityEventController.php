<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSecurityEvent;
use App\Services\SecurityEventLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SecurityEventController extends Controller {
    public function __construct(private readonly SecurityEventLogger $securityEvents) {}

    public function index(Request $request): View {
        $user = Auth::user();

        if (!($user instanceof User)) {
            abort(403);
        }

        $search = trim((string) $request->query('q', ''));
        $eventFilter = (string) $request->query('event', '');
        $roleFilter = (string) $request->query('role', '');

        if (!in_array($eventFilter, UserSecurityEvent::EVENTS, true)) {
            $eventFilter = '';
        }

        if (!in_array($roleFilter, ['A', 'G', 'S', 'C'], true)) {
            $roleFilter = '';
        }

        $eventsQuery = UserSecurityEvent::query()->with(['user:id,nama,email,status', 'actor:id,nama,email,status']);

        if ($eventFilter !== '') {
            $eventsQuery->where('event', $eventFilter);
        }

        if ($roleFilter !== '') {
            $eventsQuery->where('role', $roleFilter);
        }

        if ($search !== '') {
            $eventsQuery->where(function ($query) use ($search): void {
                $like = '%' . $search . '%';

                $query
                    ->where('email', 'like', $like)
                    ->orWhere('ip_address', 'like', $like)
                    ->orWhereHas('user', function ($userQuery) use ($like): void {
                        $userQuery->where('nama', 'like', $like)->orWhere('email', 'like', $like);
                    });
            });
        }

        $events = $eventsQuery->orderByDesc('id')->paginate(50)->withQueryString();

        $since = now()->subDay();

        $summary = [
            'login_success' => UserSecurityEvent::query()
                ->where('event', UserSecurityEvent::LOGIN_SUCCESS)
                ->where('created_at', '>=', $since)
                ->count(),
            'login_failed' => UserSecurityEvent::query()
                ->where('event', UserSecurityEvent::LOGIN_FAILED)
                ->where('created_at', '>=', $since)
                ->count(),
            'session_replaced' => UserSecurityEvent::query()
                ->where('event', UserSecurityEvent::SESSION_REPLACED)
                ->where('created_at', '>=', $since)
                ->count(),
            'session_invalidated' => UserSecurityEvent::query()
                ->where('event', UserSecurityEvent::SESSION_INVALIDATED)
                ->where('created_at', '>=', $since)
                ->count(),
        ];

        $studentSessions = User::query()
            ->where('status', 'S')
            ->where(function ($query): void {
                $query->whereNotNull('active_session_hash')->orWhereNotNull('student_session_revoked_at');
            })
            ->orderByDesc('last_login_at')
            ->limit(100)
            ->get([
                'id',
                'nama',
                'email',
                'status',
                'active_session_hash',
                'student_session_revoked_at',
                'last_login_at',
                'last_login_ip',
                'last_login_user_agent',
            ]);

        return view('admin.security-events.index', [
            'user' => $user,
            'events' => $events,
            'eventOptions' => UserSecurityEvent::EVENTS,
            'eventFilter' => $eventFilter,
            'roleFilter' => $roleFilter,
            'search' => $search,
            'summary' => $summary,
            'studentSessions' => $studentSessions,
        ]);
    }

    public function forceLogout(Request $request, User $user): RedirectResponse {
        $admin = Auth::user();

        if (!($admin instanceof User)) {
            abort(403);
        }

        if ($user->status !== 'S') {
            return back()->with('error', 'Paksa logout hanya tersedia untuk akun Siswa.');
        }

        $hadActiveSession = $user->active_session_hash !== null;

        $user
            ->forceFill([
                'active_session_hash' => null,
                'student_session_revoked_at' => now(),
            ])
            ->saveQuietly();

        $this->securityEvents->log(
            request: $request,
            event: UserSecurityEvent::SESSION_FORCE_REQUESTED,
            subject: $user,
            actor: $admin,
            metadata: [
                'had_active_session' => $hadActiveSession,
            ],
        );

        return back()->with('success', 'Session siswa ' . $user->nama . ' telah ditandai untuk dihentikan.');
    }
}
