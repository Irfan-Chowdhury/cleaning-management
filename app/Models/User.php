<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'address',
        'role',
        'photo',
        'is_active',
        'password',
        'created_by',
        'referral_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's photo URL or fallback avatar.
     */
    public function getPhotoUrlAttribute(): string
    {
        if (! empty($this->photo)) {
            if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
                return $this->photo;
            }

            $path = ltrim($this->photo, '/');
            if (! str_starts_with($path, 'public/')) {
                $path = 'public/' . $path;
            }

            return asset($path);
        }

        $name = urlencode(trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')));
        return "https://ui-avatars.com/api/?name={$name}&background=0866e8&color=fff&size=256";
    }

    /**
     * Get the wallet transactions for the user.
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get the user who created this user record.
     */
    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Send the email verification notification using the custom professional template.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }
}
