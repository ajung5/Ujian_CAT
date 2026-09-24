<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|string $id_user
 * @property string $judul
 * @property string $isi
 * @property string|null $gambar
 * @property string $status
 * @property int $hits
 * @property string|null $sesi
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Materi extends Model {
    protected $table = 'materis';

    protected $fillable = ['id_user', 'judul', 'isi', 'gambar', 'status', 'hits', 'sesi'];

    protected $casts = [
        'hits' => 'integer',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }
}
