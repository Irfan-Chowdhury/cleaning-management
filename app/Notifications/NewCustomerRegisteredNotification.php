<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCustomerRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly User $customer
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $customerName = trim($this->customer->first_name . ' ' . $this->customer->last_name);
        if (empty($customerName)) {
            $customerName = $this->customer->email;
        }

        return [
            'type'        => 'new_customer',
            'title'       => 'New customer registered',
            'message'     => "{$customerName} created a new account and requested service details.",
            'link'        => route('customers.index'),
            'icon'        => 'fas fa-user-plus',
            'customer_id' => $this->customer->id,
        ];
    }
}
