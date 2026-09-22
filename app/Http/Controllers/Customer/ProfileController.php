<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerProfileRequest;
use App\Services\CustomerProfileService;

class ProfileController extends Controller
{
    /**
     * Create a new ProfileController instance.
     */
    public function __construct(
        protected CustomerProfileService $profileService
    ) {}

    /**
     * Display customer profile settings view.
     */
    public function index()
    {
        $user = auth()->user();

        return view('pages.customer.profile', compact('user'));
    }

    /**
     * Update customer profile details.
     */
    public function update(CustomerProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $updatedUser = $this->profileService->updateProfile(
            $user,
            $request->validated(),
            $request->file('photo')
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Profile updated successfully!',
                'user' => $updatedUser,
                'photo_url' => $updatedUser->photo_url,
            ]);
        }

        return redirect()->route('customer.profile.index')->with('success', 'Profile updated successfully!');
    }
}
