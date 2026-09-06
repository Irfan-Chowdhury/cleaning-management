<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'duration_minutes',
        'status',
    ];

    public function serviceQuestions()
    {
        return $this->hasMany(ServiceQuestion::class)->orderBy('sort_order');
    }
}
