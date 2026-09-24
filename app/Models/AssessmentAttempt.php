<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $id_soal
 * @property int $id_user
 * @property int $attempt_no
 * @property string $status
 * @property float|null $score
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AssessmentAttempt extends Model {
    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_FINISHED = 'finished';

    protected $table = 'assessment_attempts';

    protected $fillable = ['id_soal', 'id_user', 'attempt_no', 'status', 'score', 'started_at', 'finished_at'];

    protected function casts(): array {
        return [
            'id_soal' => 'integer',
            'id_user' => 'integer',
            'attempt_no' => 'integer',
            'score' => 'float',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
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

    /**
     * @return HasMany<Jawab, $this>
     */
    public function jawabs(): HasMany {
        return $this->hasMany(Jawab::class, 'attempt_id');
    }

    /**
     * @return HasMany<Countexamtime, $this>
     */
    public function counters(): HasMany {
        return $this->hasMany(Countexamtime::class, 'attempt_id');
    }
}
