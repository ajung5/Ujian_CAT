<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nama
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
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
