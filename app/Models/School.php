<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $nama
 * @property string|null $alamat
 * @property string|null $logo
 * @property string|null $header
 * @property string|null $motto
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class School extends Model {
    protected $table = 'schools';

    protected $fillable = ['nama', 'alamat', 'logo', 'header', 'motto'];
}
