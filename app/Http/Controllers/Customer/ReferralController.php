<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Display customer referral program dashboard and referral history.
     */
    public function index()
    {
        $referralCode = 'IRFAN25';
        $referralLink = url('/register?ref=' . $referralCode);
        $totalReferrals = 8;
        $pendingReferrals = 2;
        $totalRewards = 500.00;

        $referrals = collect([
            (object)[
                'id'                => 1,
                'customer_name'     => 'Sarah Connor',
                'customer_avatar'   => 'https://ui-avatars.com/api/?name=Sarah+Connor&background=0D8ABC&color=fff&size=128',
                'joined_date'       => '2026-08-01',
                'status'            => 'Rewarded',
                'booking_id'        => 'BK-001',
                'reward_amount'     => 100.00,
            ],
            (object)[
                'id'                => 2,
                'customer_name'     => 'Michael Scott',
                'customer_avatar'   => 'https://ui-avatars.com/api/?name=Michael+Scott&background=2b8a3e&color=fff&size=128',
                'joined_date'       => '2026-08-03',
                'status'            => 'Rewarded',
                'booking_id'        => 'BK-002',
                'reward_amount'     => 100.00,
            ],
            (object)[
                'id'                => 3,
                'customer_name'     => 'Dwight Schrute',
                'customer_avatar'   => 'https://ui-avatars.com/api/?name=Dwight+Schrute&background=e03131&color=fff&size=128',
                'joined_date'       => '2026-08-07',
                'status'            => 'Rewarded',
                'booking_id'        => 'BK-003',
                'reward_amount'     => 100.00,
            ],
            (object)[
                'id'                => 4,
                'customer_name'     => 'Jim Halpert',
                'customer_avatar'   => 'https://ui-avatars.com/api/?name=Jim+Halpert&background=f59f00&color=fff&size=128',
                'joined_date'       => '2026-08-10',
                'status'            => 'Rewarded',
                'booking_id'        => 'BK-004',
                'reward_amount'     => 100.00,
            ],
            (object)[
                'id'                => 5,
                'customer_name'     => 'Pam Beesly',
                'customer_avatar'   => 'https://ui-avatars.com/api/?name=Pam+Beesly&background=9c36b5&color=fff&size=128',
                'joined_date'       => '2026-08-12',
                'status'            => 'Rewarded',
                'booking_id'        => 'BK-005',
                'reward_amount'     => 100.00,
            ],
            (object)[
                'id'                => 6,
                'customer_name'     => 'Ryan Howard',
                'customer_avatar'   => 'https://ui-avatars.com/api/?name=Ryan+Howard&background=1098ad&color=fff&size=128',
                'joined_date'       => '2026-08-15',
                'status'            => 'Approved',
                'booking_id'        => 'BK-006',
                'reward_amount'     => 0.00,
            ],
            (object)[
                'id'                => 7,
                'customer_name'     => 'Kelly Kapoor',
                'customer_avatar'   => 'https://ui-avatars.com/api/?name=Kelly+Kapoor&background=d63384&color=fff&size=128',
                'joined_date'       => '2026-08-18',
                'status'            => 'Pending',
                'booking_id'        => null,
                'reward_amount'     => 0.00,
            ],
            (object)[
                'id'                => 8,
                'customer_name'     => 'Andy Bernard',
                'customer_avatar'   => 'https://ui-avatars.com/api/?name=Andy+Bernard&background=6c757d&color=fff&size=128',
                'joined_date'       => '2026-08-19',
                'status'            => 'Registered',
                'booking_id'        => null,
                'reward_amount'     => 0.00,
            ],
        ]);

        return view('pages.customer.refferal.index', compact(
            'referralCode',
            'referralLink',
            'totalReferrals',
            'pendingReferrals',
            'totalRewards',
            'referrals'
        ));
    }
}
