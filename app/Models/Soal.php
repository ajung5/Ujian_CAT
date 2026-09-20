<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Soal extends Model
{
    protected $table = 'soals';

    protected $fillable = [
        'id_user',
        'jenis',
        'materi',
        'paket',
        'deskripsi',
        'kkm',
        'waktu',
        'tampil',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function detailsoals(): HasMany
    {
        return $this->hasMany(Detailsoal::class, 'id_soal');
    }

    public function distribusisoals(): HasMany
    {
        return $this->hasMany(Distribusisoal::class, 'id_soal');
    }

    public function jawabs(): HasMany
    {
        return $this->hasMany(Jawab::class, 'id_soal');
    }

    public function countexamtimes(): HasMany
    {
        return $this->hasMany(Countexamtime::class, 'id_soal');
    }

    public function materiData(): BelongsTo
    {
        return $this->belongsTo(Materi::class, 'materi');
    }
}