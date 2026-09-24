<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|string $no_soal_id
 * @property string $id_soal
 * @property string $id_user
 * @property string|null $id_kelas
 * @property string|null $nama
 * @property string $pilihan
 * @property string|null $score
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Jawab extends Model {
    protected $table = 'jawabs';

    protected $fillable = ['no_soal_id', 'id_soal', 'id_user', 'id_kelas', 'nama', 'pilihan', 'score', 'status'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * @return BelongsTo<Soal, $this>
     */
    public function soal(): BelongsTo {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    /**
     * @return BelongsTo<Kelas, $this>
     */
    public function kelas(): BelongsTo {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
