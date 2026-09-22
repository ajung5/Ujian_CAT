<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jawab extends Model {
    protected $table = 'jawabs';

    protected $fillable = ['no_soal_id', 'id_soal', 'id_user', 'id_kelas', 'nama', 'pilihan', 'score', 'status'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function soal(): BelongsTo {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function kelas(): BelongsTo {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
