<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index()
    {
        $customers = $this->mockData();
        return view('pages.admin.customers.index', compact('customers'));
    }

    public function mockData()
    {
        // Static mock data for customers representing role = 2 (Customers)
        return collect([
            [
                'id' => 1,
                'name' => 'Irfan Chowdhury',
                'first_name' => 'Irfan',
                'last_name' => 'Chowdhury',
                'email' => 'irfan@example.com',
                'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg',
                'phone' => '+880 1712-345678',
                'gender' => 'male',
                'referral_code' => 'IRFAN9525',
                'bookings_count' => 15,
                'wallet_balance' => 120.00,
                'referred_count' => 5,
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Sarah Connor',
                'first_name' => 'Sarah',
                'last_name' => 'Connor',
                'email' => 'sarah.c@example.com',
                'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg',
                'phone' => '+1 (202) 555-0143',
                'gender' => 'female',
                'referral_code' => 'SARA3320',
                'bookings_count' => 8,
                'wallet_balance' => 45.50,
                'referred_count' => 2,
                'status' => 'active',
            ],
            [
                'id' => 3,
                'name' => 'John Doe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'avatar' => 'https://randomuser.me/api/portraits/men/12.jpg',
                'phone' => '+1 (312) 555-0188',
                'gender' => 'male',
                'referral_code' => 'JOHN7741',
                'bookings_count' => 3,
                'wallet_balance' => 0.00,
                'referred_count' => 0,
                'status' => 'inactive',
            ],
            [
                'id' => 4,
                'name' => 'Emily Watson',
                'first_name' => 'Emily',
                'last_name' => 'Watson',
                'email' => 'emily.w@example.com',
                'avatar' => 'https://randomuser.me/api/portraits/women/17.jpg',
                'phone' => '+44 20 7946 0958',
                'gender' => 'female',
                'referral_code' => 'EMIL1954',
                'bookings_count' => 24,
                'wallet_balance' => 310.25,
                'referred_count' => 11,
                'status' => 'active',
            ],
            [
                'id' => 5,
                'name' => 'David Beckham',
                'first_name' => 'David',
                'last_name' => 'Beckham',
                'email' => 'david.b@example.com',
                'avatar' => 'https://randomuser.me/api/portraits/men/45.jpg',
                'phone' => '+44 20 7946 0192',
                'gender' => 'male',
                'referral_code' => 'DAVI8872',
                'bookings_count' => 0,
                'wallet_balance' => 15.75,
                'referred_count' => 1,
                'status' => 'inactive',
            ]
        ]);
    }
}
