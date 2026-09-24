<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $id_user
 * @property string $jenis
 * @property int|null $materi
 * @property string $paket
 * @property string $deskripsi
 * @property string $kkm
 * @property string $waktu
 * @property string|null $tampil
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Soal extends Model {
    protected $table = 'soals';

    protected $fillable = ['id_user', 'jenis', 'materi', 'paket', 'deskripsi', 'kkm', 'waktu', 'tampil'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function detailsoals(): HasMany {
        return $this->hasMany(Detailsoal::class, 'id_soal');
    }

    public function distribusisoals(): HasMany {
        return $this->hasMany(Distribusisoal::class, 'id_soal');
    }

    public function jawabs(): HasMany {
        return $this->hasMany(Jawab::class, 'id_soal');
    }

    public function countexamtimes(): HasMany {
        return $this->hasMany(Countexamtime::class, 'id_soal');
    }

    public function materiData(): BelongsTo {
        return $this->belongsTo(Materi::class, 'materi');
    }
}
