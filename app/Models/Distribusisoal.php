<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distribusisoal extends Model
{
    protected $table = 'distribusisoals';

    protected $fillable = [
        'id_soal',
        'id_kelas',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}