<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table = 'schools';

    protected $fillable = [
        'nama',
        'alamat',
        'logo',
        'header',
        'motto',
    ];
}