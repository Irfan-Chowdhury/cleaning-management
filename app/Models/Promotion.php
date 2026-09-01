<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_type',
        'discount_value',
        'status',
        'start_at',
        'expires_at',
        'new_customers_only',
        'existing_customers_only',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'start_at' => 'datetime',
            'expires_at' => 'datetime',
            'new_customers_only' => 'boolean',
            'existing_customers_only' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
