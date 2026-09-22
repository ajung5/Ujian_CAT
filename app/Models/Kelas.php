<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model {
    protected $table = 'kelas';

    protected $fillable = ['nama'];

    public function users(): HasMany {
        return $this->hasMany(User::class, 'id_kelas');
    }

    public function distribusisoals(): HasMany {
        return $this->hasMany(Distribusisoal::class, 'id_kelas');
    }

    public function jawabs(): HasMany {
        return $this->hasMany(Jawab::class, 'id_kelas');
    }
}
