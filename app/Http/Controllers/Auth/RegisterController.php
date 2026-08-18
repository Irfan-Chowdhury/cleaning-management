<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        // If the user is already logged in, redirect them to the dashboard
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
        $customer = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'role' => 2, // 1 = admin, 2 = customer
            'password' => Hash::make($request->password),
        ]);

        $customer->update([
            'referral_code' => strtoupper($customer->first_name . $customer->id),
        ]);

        Auth::login($customer);

        return redirect()->route('dashboard')->with('success', 'Registration successful. Welcome to Dust2Glow!');
    }
}
