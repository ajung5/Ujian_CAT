<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $attempt_id
 * @property string $id_soal
 * @property string $id_user
 * @property string $waktu
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Countexamtime extends Model {
    protected $table = 'countexamtimes';

    protected $fillable = ['attempt_id', 'id_soal', 'id_user', 'waktu'];

    /**
     * @return BelongsTo<AssessmentAttempt, $this>
     */
    public function attempt(): BelongsTo {
        return $this->belongsTo(AssessmentAttempt::class, 'attempt_id');
    }

    /**
     * @return BelongsTo<Soal, $this>
     */
    public function soal(): BelongsTo {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }
}
