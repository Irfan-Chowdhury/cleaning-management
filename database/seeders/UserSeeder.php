<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->delete();

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@gmail.com',
            'phone' => '+8801700000001',
            'gender' => 'male',
            'role' => 1,
            'password' => Hash::make('admin'),
        ]);

        $customer = User::create([
            'first_name' => 'Irfan',
            'last_name' => 'Customer',
            'email' => 'customer@gmail.com',
            'phone' => '+8801700000002',
            'gender' => 'male',
            'role' => 2,
            'password' => Hash::make('customer'),
            'created_by' => $admin->id,
        ]);

        $customer->update([
            'referral_code' => strtoupper($customer->first_name . $customer->id),
        ]);
    }
}
