<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationClient extends Model
{
    use Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quotation_clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'contact_person',
        'company_name',
        'phone',
        'email',
        'address',
        'created_by',
    ];

    /**
     * Get the user that created this client profile.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
