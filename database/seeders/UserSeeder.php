<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->delete();

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name'  => 'User',
            'email'      => 'admin@gmail.com',
            'phone'      => '+8801700000001',
            'gender'     => 'male',
            'address'    => '742 Evergreen Terrace, Springfield',
            'role'       => 1,
            'is_active'  => true,
            'password'   => Hash::make('admin'),
        ]);

        $customer = User::create([
            'first_name' => 'Irfan',
            'last_name'  => 'Customer',
            'email'      => 'customer@gmail.com',
            'phone'      => '+8801700000002',
            'gender'     => 'male',
            'address'    => '123 Cleaning Avenue, Suite 404',
            'role'       => 2,
            'is_active'  => true,
            'password'   => Hash::make('customer'),
            'created_by' => $admin->id,
        ]);

        $referralCode = strtoupper($customer->first_name . $customer->id);

        $customer->update([
            'referral_code' => $referralCode,
        ]);

        // Primary booking for customer@gmail.com (completed status)
        Booking::create([
            'user_id'          => $customer->id,
            'service_id'       => 1,
            'booking_date'     => now()->subDays(15)->format('Y-m-d'),
            'start_time'       => '09:00:00',
            'end_time'         => '11:00:00',
            'customer_name'    => $customer->first_name . ' ' . $customer->last_name,
            'customer_email'   => $customer->email,
            'customer_phone'   => $customer->phone,
            'customer_address' => $customer->address,
            'status'           => 'completed',
            'payment_status'   => 'paid',
            'payment_method'   => 'card',
            'subtotal'         => 150.00,
            'total_amount'     => 150.00,
        ]);

        // Referred Customer 1: Sarah Connor (Completed Booking using referral code)
        $refUser1 = User::create([
            'first_name' => 'Sarah',
            'last_name'  => 'Connor',
            'email'      => 'sarah.connor@example.com',
            'phone'      => '+8801700000003',
            'gender'     => 'female',
            'address'    => '456 Cyberdyne Way',
            'role'       => 2,
            'is_active'  => true,
            'password'   => Hash::make('password'),
            'created_by' => $admin->id,
        ]);
        $refUser1->update([
            'referral_code' => strtoupper($refUser1->first_name . $refUser1->id),
        ]);

        Booking::create([
            'user_id'          => $refUser1->id,
            'service_id'       => 1,
            'booking_date'     => now()->subDays(8)->format('Y-m-d'),
            'start_time'       => '10:00:00',
            'end_time'         => '12:00:00',
            'customer_name'    => 'Sarah Connor',
            'customer_email'   => 'sarah.connor@example.com',
            'customer_phone'   => '+8801700000003',
            'customer_address' => '456 Cyberdyne Way',
            'status'           => 'completed',
            'payment_status'   => 'paid',
            'payment_method'   => 'card',
            'subtotal'         => 120.00,
            'discount_amount'  => 25.00,
            'total_amount'     => 95.00,
            'referal_code'     => $referralCode,
        ]);

        WalletTransaction::create([
            'user_id'     => $customer->id,
            'type'        => 'credit',
            'amount'      => 25.00,
            'source'      => 'referral_bonus',
            'description' => 'Referral Reward for customer Sarah Connor',
        ]);

        // Referred Customer 2: Michael Scott (Completed Booking using referral code)
        $refUser2 = User::create([
            'first_name' => 'Michael',
            'last_name'  => 'Scott',
            'email'      => 'michael.scott@example.com',
            'phone'      => '+8801700000004',
            'gender'     => 'male',
            'address'    => '1725 Slough Avenue, Scranton',
            'role'       => 2,
            'is_active'  => true,
            'password'   => Hash::make('password'),
            'created_by' => $admin->id,
        ]);
        $refUser2->update([
            'referral_code' => strtoupper($refUser2->first_name . $refUser2->id),
        ]);

        Booking::create([
            'user_id'          => $refUser2->id,
            'service_id'       => 2,
            'booking_date'     => now()->subDays(3)->format('Y-m-d'),
            'start_time'       => '14:00:00',
            'end_time'         => '16:00:00',
            'customer_name'    => 'Michael Scott',
            'customer_email'   => 'michael.scott@example.com',
            'customer_phone'   => '+8801700000004',
            'customer_address' => '1725 Slough Avenue, Scranton',
            'status'           => 'completed',
            'payment_status'   => 'paid',
            'payment_method'   => 'card',
            'subtotal'         => 200.00,
            'discount_amount'  => 25.00,
            'total_amount'     => 175.00,
            'referal_code'     => $referralCode,
        ]);

        WalletTransaction::create([
            'user_id'     => $customer->id,
            'type'        => 'credit',
            'amount'      => 25.00,
            'source'      => 'referral_bonus',
            'description' => 'Referral Reward for customer Michael Scott',
        ]);

        // Referred Customer 3: Dwight Schrute (Pending Booking using referral code)
        $refUser3 = User::create([
            'first_name' => 'Dwight',
            'last_name'  => 'Schrute',
            'email'      => 'dwight.schrute@example.com',
            'phone'      => '+8801700000005',
            'gender'     => 'male',
            'address'    => 'Beet Farms, Scranton',
            'role'       => 2,
            'is_active'  => true,
            'password'   => Hash::make('password'),
            'created_by' => $admin->id,
        ]);
        $refUser3->update([
            'referral_code' => strtoupper($refUser3->first_name . $refUser3->id),
        ]);

        Booking::create([
            'user_id'          => $refUser3->id,
            'service_id'       => 1,
            'booking_date'     => now()->addDays(2)->format('Y-m-d'),
            'start_time'       => '11:00:00',
            'end_time'         => '13:00:00',
            'customer_name'    => 'Dwight Schrute',
            'customer_email'   => 'dwight.schrute@example.com',
            'customer_phone'   => '+8801700000005',
            'customer_address' => 'Beet Farms, Scranton',
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
            'payment_method'   => 'cash',
            'subtotal'         => 150.00,
            'discount_amount'  => 25.00,
            'total_amount'     => 125.00,
            'referal_code'     => $referralCode,
        ]);
    }
}
