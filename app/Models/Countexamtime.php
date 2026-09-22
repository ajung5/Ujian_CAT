<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Countexamtime extends Model {
    protected $table = 'countexamtimes';

    protected $fillable = ['id_soal', 'id_user', 'waktu'];

    public function soal(): BelongsTo {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }
}
