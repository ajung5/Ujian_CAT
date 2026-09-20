<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materi extends Model
{
    protected $table = 'materis';

    protected $fillable = [
        'id_user',
        'judul',
        'isi',
        'gambar',
        'status',
        'hits',
        'sesi',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'materi');
    }
}