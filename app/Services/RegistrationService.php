<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\NewCustomerRegisteredNotification;
use App\Notifications\WelcomeBonusNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class RegistrationService
{
    /**
     * Register a new customer user and send email verification notification.
     */
    public function register(array $data): User
    {
        $refCode = !empty($data['referred_by_code']) ? strtoupper(trim($data['referred_by_code'])) : null;
        if ($refCode) {
            $referrerExists = User::where('referral_code', $refCode)->exists();
            if (!$referrerExists) {
                $refCode = null;
            }
        }

        $user = User::create([
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'] ?? null,
            'email'            => $data['email'],
            'phone'            => $data['phone'] ?? null,
            'gender'           => $data['gender'] ?? null,
            'address'          => $data['address'] ?? null,
            'role'             => 2, // 2 = Customer
            'password'         => Hash::make($data['password']),
            'referred_by_code' => $refCode,
        ]);

        $user->update([
            'referral_code' => strtoupper($user->first_name . $user->id),
        ]);

        // Link registration to pending referral record or code in referrals table
        $this->linkReferralOnRegistration($user);

        // Notify admins about new customer registration
        $admins = User::where('role', 1)->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewCustomerRegisteredNotification($user));
        }

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

        // Send professional welcome email + DB notification with bonus details
        $user->notify(new WelcomeBonusNotification($bonusAmount));

        return true;
    }

    /**
     * Link newly registered user to pending invitation or referrer code in referrals table.
     */
    protected function linkReferralOnRegistration(User $user): void
    {
        try {
            $userEmail = strtolower(trim($user->email));
            $invitedReferral = Referral::where('recipient_email', $userEmail)
                ->where('status', 'invited')
                ->first();

            if ($invitedReferral) {
                $invitedReferral->update([
                    'referred_user_id' => $user->id,
                    'status'           => 'signed_up',
                ]);
            } elseif (!empty($user->referred_by_code)) {
                $referrer = User::where('referral_code', $user->referred_by_code)->first();
                if ($referrer && $referrer->id !== $user->id) {
                    Referral::create([
                        'referrer_user_id' => $referrer->id,
                        'referral_code'    => $referrer->referral_code,
                        'recipient_email'  => $userEmail,
                        'referred_user_id' => $user->id,
                        'status'           => 'signed_up',
                    ]);
                }
            }
        } catch (Throwable $e) {
            Log::error('Failed to link referral in referrals table on registration: ' . $e->getMessage());
        }
    }
}
