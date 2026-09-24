<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $id_soal
 * @property string $jenis
 * @property string $soal
 * @property string|null $audio
 * @property string $pila
 * @property string $pilb
 * @property string $pilc
 * @property string $pild
 * @property string $pile
 * @property string $kunci
 * @property string|null $score
 * @property string $id_user
 * @property string $status
 * @property string|null $sesi
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
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

    /**
     * @return BelongsTo<Soal, $this>
     */
    public function soalInduk(): BelongsTo {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }
}
