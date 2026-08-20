<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display customer profile settings view.
     */
    public function index()
    {
        $user = (object)[
            'id'            => 1,
            'name'          => 'Irfan Chowdhury',
            'email'         => 'irfan@example.com',
            'phone'         => '+1 (555) 234-5678',
            'gender'        => 'male',
            'referral_code' => 'IRFAN25',
            'avatar'        => 'https://ui-avatars.com/api/?name=Irfan+Chowdhury&background=0866e8&color=fff&size=256',
        ];

        return view('pages.customer.profile', compact('user'));
    }
}
