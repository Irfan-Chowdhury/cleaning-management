<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function __construct(private readonly RegistrationService $registrationService)
    {
    }

    /**
     * Display the email verification notice view.
     */
    public function notice()
    {
        if (Auth::user()?->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('info', 'Your email address is already verified.');
        }

        $this->registrationService->handleEmailVerification($user);

        if (! Auth::check()) {
            Auth::login($user);
        }

        return redirect()->route('dashboard')->with('success', 'Email verified successfully! Welcome bonus credit has been added to your wallet.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'A new verification link has been sent to your email address.');
    }
}
