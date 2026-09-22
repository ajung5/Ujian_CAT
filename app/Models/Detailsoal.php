<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Detailsoal extends Model {
    protected $table = 'detailsoals';

    protected $fillable = [
        'id_soal',
        'jenis',
        'soal',
        'audio',
        'pila',
        'pilb',
        'pilc',
        'pild',
        'pile',
        'kunci',
        'score',
        'id_user',
        'status',
        'sesi',
    ];

    public function soalInduk(): BelongsTo {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }
}
