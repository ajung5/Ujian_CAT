<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    protected $hidden = ['password', 'remember_token'];

    public function soals(): HasMany {
        return $this->hasMany(Soal::class, 'id_user');
    }

    public function jawabs(): HasMany {
        return $this->hasMany(Jawab::class, 'id_user');
    }

    public function aktifitas(): HasMany {
        return $this->hasMany(Aktifitas::class, 'id_user');
    }

    public function countexamtimes(): HasMany {
        return $this->hasMany(Countexamtime::class, 'id_user');
    }
}
