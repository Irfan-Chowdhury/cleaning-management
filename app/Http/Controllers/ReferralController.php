<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $referrals = collect([
            (object)[
                'id'              => 1,
                'referrer_name'   => 'John Doe',
                'referrer_email'  => 'john@example.com',
                'referrer_avatar' => 'https://ui-avatars.com/api/?name=John+Doe&background=0D8ABC&color=fff&size=128',
                'referred_name'   => 'Mary Jane',
                'referred_email'  => 'mary@example.com',
                'referred_avatar' => 'https://ui-avatars.com/api/?name=Mary+Jane&background=9c36b5&color=fff&size=128',
                'referral_code'   => 'REF-JOHN25',
                'reward_amount'   => 25.00,
                'booking_service' => 'Deep Home Cleaning',
                'created_at'      => '2026-08-15 10:30:00',
            ],
            (object)[
                'id'              => 2,
                'referrer_name'   => 'Alice Johnson',
                'referrer_email'  => 'alice@example.com',
                'referrer_avatar' => 'https://ui-avatars.com/api/?name=Alice+Johnson&background=2b8a3e&color=fff&size=128',
                'referred_name'   => 'Robert Smith',
                'referred_email'  => 'robert@example.com',
                'referred_avatar' => 'https://ui-avatars.com/api/?name=Robert+Smith&background=f59f00&color=fff&size=128',
                'referral_code'   => 'REF-ALICE10',
                'reward_amount'   => 30.00,
                'booking_service' => 'Office Sanitation Service',
                'created_at'      => '2026-08-16 14:15:00',
            ],
            (object)[
                'id'              => 3,
                'referrer_name'   => 'David Brown',
                'referrer_email'  => 'david@example.com',
                'referrer_avatar' => 'https://ui-avatars.com/api/?name=David+Brown&background=e03131&color=fff&size=128',
                'referred_name'   => 'Sarah Connor',
                'referred_email'  => 'sarah@example.com',
                'referred_avatar' => 'https://ui-avatars.com/api/?name=Sarah+Connor&background=1098ad&color=fff&size=128',
                'referral_code'   => 'REF-DAVID15',
                'reward_amount'   => 20.00,
                'booking_service' => 'Carpet Wash & Steam',
                'created_at'      => '2026-08-17 09:45:00',
            ],
            (object)[
                'id'              => 4,
                'referrer_name'   => 'Eva Martinez',
                'referrer_email'  => 'eva@example.com',
                'referrer_avatar' => 'https://ui-avatars.com/api/?name=Eva+Martinez&background=f783ac&color=fff&size=128',
                'referred_name'   => 'Michael Scott',
                'referred_email'  => 'michael@example.com',
                'referred_avatar' => 'https://ui-avatars.com/api/?name=Michael+Scott&background=3b5bdb&color=fff&size=128',
                'referral_code'   => 'REF-EVA50',
                'reward_amount'   => 50.00,
                'booking_service' => 'Move-in / Move-out Cleaning',
                'created_at'      => '2026-08-18 16:20:00',
            ],
            (object)[
                'id'              => 5,
                'referrer_name'   => 'Frank Lee',
                'referrer_email'  => 'frank@example.com',
                'referrer_avatar' => 'https://ui-avatars.com/api/?name=Frank+Lee&background=20c997&color=fff&size=128',
                'referred_name'   => 'Pam Beesly',
                'referred_email'  => 'pam@example.com',
                'referred_avatar' => 'https://ui-avatars.com/api/?name=Pam+Beesly&background=fd7e14&color=fff&size=128',
                'referral_code'   => 'REF-FRANK05',
                'reward_amount'   => 15.00,
                'booking_service' => 'Window Washing',
                'created_at'      => '2026-08-19 11:10:00',
            ],
        ]);

        return view('pages.admin.referrals.index', compact('referrals'));
    }
}
