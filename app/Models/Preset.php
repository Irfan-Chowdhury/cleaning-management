<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preset extends Model
{
    protected $fillable = ['name', 'devices_json'];

    protected $casts = [
        'devices_json' => 'array', // JSON → array auto cast
    ];
}
