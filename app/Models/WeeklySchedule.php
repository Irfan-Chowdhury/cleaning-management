<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklySchedule extends Model
{
    use HasFactory, Auditable;

    protected $table = 'weekly_schedule';

    protected $fillable = [
        'day_of_week',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function slots(): HasMany
    {
        return $this->hasMany(ScheduleSlot::class)->orderBy('sort_order')->orderBy('start_time');
    }
}
