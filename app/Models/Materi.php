<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $casts = [
        'hits' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_user'
        );
    }
}