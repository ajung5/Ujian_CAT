<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aktifitas extends Model {
    protected $table = 'aktifitas';

    protected $fillable = ['id_user', 'nama'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user');
    }
}
