<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\RegistrationService;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(private readonly RegistrationService $registrationService)
    {
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(RegisterRequest $request)
    {
        $customer = $this->registrationService->register($request->validated());

        Auth::login($customer);

        return redirect()->route('verification.notice')
            ->with('success', 'Registration successful! A verification email has been sent to your email address.');

        // $targetRoute = $customer->hasVerifiedEmail() ? 'dashboard' : 'verification.notice';
        // return redirect()->route('login')->with('success', 'Registration successful!');
    }
}
