<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $table = 'question_options';

    public $timestamps = false;

    protected $fillable = [
        'service_question_id',
        'label',
    ];

    public function serviceQuestion()
    {
        return $this->belongsTo(ServiceQuestion::class);
    }
}
