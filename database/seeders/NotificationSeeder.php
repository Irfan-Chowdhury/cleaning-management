<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\NewCustomerRegisteredNotification;
use App\Notifications\WelcomeBonusNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 1)->first();
        $customer = User::where('role', 2)->first();

        if ($customer) {
            // Seed Customer Welcome & Wallet Notifications
            $customer->notify(new WelcomeBonusNotification(50.00));

            $customer->notifications()->create([
                'id'              => \Illuminate\Support\Str::uuid(),
                'type'            => 'App\Notifications\GeneralNotification',
                'notifiable_type' => User::class,
                'notifiable_id'   => $customer->id,
                'data'            => [
                    'type'    => 'booking_confirmed',
                    'title'   => 'New booking confirmed',
                    'message' => 'Your home cleaning service has been scheduled for tomorrow morning.',
                    'link'    => route('customer.bookings.index'),
                    'icon'    => 'fas fa-calendar-check',
                ],
                'created_at'      => now()->subDays(2),
                'updated_at'      => now()->subDays(2),
            ]);
        }

        if ($admin) {
            // Seed Admin Notifications (New Customer Registered, etc.)
            if ($customer) {
                $admin->notify(new NewCustomerRegisteredNotification($customer));
            }

            $admin->notifications()->create([
                'id'              => \Illuminate\Support\Str::uuid(),
                'type'            => 'App\Notifications\GeneralNotification',
                'notifiable_type' => User::class,
                'notifiable_id'   => $admin->id,
                'data'            => [
                    'type'    => 'new_customer',
                    'title'   => 'New customer registered',
                    'message' => 'Rahim Chowdhury created an account and requested service details.',
                    'link'    => route('customers.index'),
                    'icon'    => 'fas fa-user-plus',
                ],
                'created_at'      => now()->subHours(5),
                'updated_at'      => now()->subHours(5),
            ]);
        }
    }
}
