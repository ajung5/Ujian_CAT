<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $actor_user_id
 * @property string|null $email
 * @property string|null $role
 * @property string $event
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $session_hash
 * @property array<string, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 */
class UserSecurityEvent extends Model {
    public const LOGIN_SUCCESS = 'LOGIN_SUCCESS';

    public const LOGIN_FAILED = 'LOGIN_FAILED';

    public const LOGOUT = 'LOGOUT';

    public const SESSION_REPLACED = 'SESSION_REPLACED';

    public const SESSION_INVALIDATED = 'SESSION_INVALIDATED';

    public const SESSION_FORCE_REQUESTED = 'SESSION_FORCE_REQUESTED';

    public const SESSION_LEGACY_CLAIMED = 'SESSION_LEGACY_CLAIMED';

    /**
     * @var list<string>
     */
    public const EVENTS = [
        self::LOGIN_SUCCESS,
        self::LOGIN_FAILED,
        self::LOGOUT,
        self::SESSION_REPLACED,
        self::SESSION_INVALIDATED,
        self::SESSION_FORCE_REQUESTED,
        self::SESSION_LEGACY_CLAIMED,
    ];

    public $timestamps = false;

    protected $table = 'user_security_events';

    protected $fillable = [
        'user_id',
        'actor_user_id',
        'email',
        'role',
        'event',
        'ip_address',
        'user_agent',
        'session_hash',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * User yang menjadi subjek event.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * User yang melakukan aksi, jika ada.
     *
     * @return BelongsTo<User, $this>
     */
    public function actor(): BelongsTo {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
