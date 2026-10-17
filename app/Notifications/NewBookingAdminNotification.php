<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NewBookingAdminNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Booking $booking
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $setting = Setting::first();
        $companyName = $setting?->company_name ?: config('app.name', 'Cleaning Management');
        $serviceName = $this->booking->service?->name ?? 'Cleaning Service';

        $formattedDate = $this->booking->booking_date
            ? Carbon::parse($this->booking->booking_date)->format('D, d M Y')
            : 'Date to be scheduled';

        $formattedTime = $this->booking->start_time
            ? (Carbon::parse($this->booking->start_time)->format('g:i A') . ($this->booking->end_time ? ' - ' . Carbon::parse($this->booking->end_time)->format('g:i A') : ''))
            : 'Time to be scheduled';

        $fullAddress = trim(
            ($this->booking->unit_suite_floor ? $this->booking->unit_suite_floor . ', ' : '') .
            ($this->booking->customer_address ?? '') .
            ($this->booking->suburb ? ', ' . $this->booking->suburb : '') .
            ($this->booking->postcode ? ' ' . $this->booking->postcode : '')
        );

        return (new MailMessage)
            ->subject("New Booking Request #{$this->booking->id} - {$serviceName}")
            ->greeting('Hello Admin,')
            ->line("A new cleaning booking request has been submitted by **{$this->booking->customer_name}**.")
            ->line(new HtmlString("<strong>Booking Details:</strong><br>" .
                "&bull; <strong>Booking ID:</strong> #{$this->booking->id}<br>" .
                "&bull; <strong>Service:</strong> " . e($serviceName) . "<br>" .
                "&bull; <strong>Customer:</strong> " . e($this->booking->customer_name) . " (" . e($this->booking->customer_email) . ")<br>" .
                "&bull; <strong>Phone:</strong> " . e($this->booking->customer_phone ?: 'N/A') . "<br>" .
                "&bull; <strong>Date & Time:</strong> {$formattedDate} at {$formattedTime}<br>" .
                "&bull; <strong>Address:</strong> " . e($fullAddress ?: 'N/A')))
            ->action('View & Manage Booking', route('bookings.edit', $this->booking->id))
            ->line('Please review this quote request in the admin panel and set the final price for confirmation.')
            ->salutation(new HtmlString("Regards,<br>" . e($companyName)));
    }
}
