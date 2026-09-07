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
            'address' => '742 Evergreen Terrace, Springfield',
            'role' => 1,
            'is_active' => true,
            'password' => Hash::make('admin'),
        ]);

        $customer = User::create([
            'first_name' => 'Irfan',
            'last_name' => 'Customer',
            'email' => 'customer@gmail.com',
            'phone' => '+8801700000002',
            'gender' => 'male',
            'address' => '123 Cleaning Avenue, Suite 404',
            'role' => 2,
            'is_active' => true,
            'password' => Hash::make('customer'),
            'created_by' => $admin->id,
        ]);

        $customer->update([
            'referral_code' => strtoupper($customer->first_name . $customer->id),
        ]);
    }
}
