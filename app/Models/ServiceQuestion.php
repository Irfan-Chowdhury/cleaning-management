<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceQuestion extends Model
{
    use HasFactory, Auditable;

    public $timestamps = false;

    protected $fillable = [
        'service_id',
        'title',
        'field_type',
        'required',
        'sort_order',
    ];

    protected $casts = [
        'required' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function questionOptions()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('id');
    }
}
