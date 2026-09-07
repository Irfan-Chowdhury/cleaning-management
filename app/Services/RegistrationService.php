<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\WelcomeBonusNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    /**
     * Register a new customer user and send email verification notification.
     */
    public function register(array $data): User
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'] ?? null,
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'gender'     => $data['gender'] ?? null,
            'address'    => $data['address'] ?? null,
            'role'       => 2, // 2 = Customer
            'password'   => Hash::make($data['password']),
        ]);

        $user->update([
            'referral_code' => strtoupper($user->first_name . $user->id),
        ]);

        // Fire Registered event — automatically triggers sendEmailVerificationNotification()
        // for users implementing MustVerifyEmail. Do NOT call it manually here.
        event(new Registered($user));

        return $user;
    }

    /**
     * Handle email verification and allocate welcome credit if enabled.
     */
    public function handleEmailVerification(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $bonusAmount = 0.00;

        // Check settings for welcome credit
        $setting = Setting::first();
        if ($setting && $setting->welcome_credit_enabled && (float) $setting->welcome_credit > 0) {
            $alreadyCredited = WalletTransaction::where('user_id', $user->id)
                ->where('source', 'welcome_bonus')
                ->exists();

            if (! $alreadyCredited) {
                $bonusAmount = (float) $setting->welcome_credit;
                WalletTransaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'credit',
                    'amount'      => $bonusAmount,
                    'source'      => 'welcome_bonus',
                    'description' => 'Welcome Registration Bonus Credit',
                ]);
            }
        }

        // Send professional welcome email with bonus details
        $user->notify(new WelcomeBonusNotification($bonusAmount));

        return true;
    }
}
