<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'quote_number',
        'quote_date',
        'abn',
        'contact_person',
        'company_name',
        'address',
        'phone',
        'email',
        'frequency',
        'shift_schedule',
        'subtotal',
        'gst_amount',
        'total_amount',
        'terms_notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quote_date'   => 'date',
            'subtotal'     => 'decimal:2',
            'gst_amount'   => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the line items associated with this quotation.
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order', 'asc');
    }
}
