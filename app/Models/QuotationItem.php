<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'quotation_id',
        'description',
        'amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the quotation that owns this item.
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}
