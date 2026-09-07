<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory, Auditable;

    protected $table = 'wallet_transactions';

    protected $fillable = [
        'user_id',
        'booking_id',
        'type',
        'amount',
        'source',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
            'booking_id' => 'integer',
        ];
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
