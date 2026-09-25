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
}
