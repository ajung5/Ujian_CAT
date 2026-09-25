<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property int|string|null $id_kelas
 * @property string $nama
 * @property string|null $no_induk
 * @property string|null $jk
 * @property string $status
 * @property string $gambar
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property string $sekolah_asal
 * @property string|null $active_session_hash
 * @property \Illuminate\Support\Carbon|null $student_session_revoked_at
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property string|null $last_login_user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable {
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'id_kelas',
        'nama',
        'no_induk',
        'jk',
        'status',
        'gambar',
        'email',
        'password',
        'sekolah_asal',
    ];

    protected $hidden = ['password', 'remember_token', 'active_session_hash'];

    protected $casts = [
        'student_session_revoked_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * @return HasMany<Soal, $this>
     */
    public function soals(): HasMany {
        return $this->hasMany(Soal::class, 'id_user');
    }

    /**
     * @return HasMany<Jawab, $this>
     */
    public function jawabs(): HasMany {
        return $this->hasMany(Jawab::class, 'id_user');
    }

    /**
     * @return HasMany<Aktifitas, $this>
     */
    public function aktifitas(): HasMany {
        return $this->hasMany(Aktifitas::class, 'id_user');
    }

    /**
     * @return HasMany<Countexamtime, $this>
     */
    public function countexamtimes(): HasMany {
        return $this->hasMany(Countexamtime::class, 'id_user');
    }

    /**
     * @return HasMany<UserSecurityEvent, $this>
     */
    public function securityEvents(): HasMany {
        return $this->hasMany(UserSecurityEvent::class, 'user_id');
    }

    /**
     * @return HasMany<UserSecurityEvent, $this>
     */
    public function securityActions(): HasMany {
        return $this->hasMany(UserSecurityEvent::class, 'actor_user_id');
    }
}
