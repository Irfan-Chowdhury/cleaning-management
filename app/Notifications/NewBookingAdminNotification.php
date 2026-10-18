<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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

        $actionUrl = route('bookings.edit', $this->booking->id);

        return (new MailMessage)
            ->subject("New Booking Request #{$this->booking->id} - {$serviceName}")
            ->view('emails.new_booking_admin', [
                'booking'       => $this->booking,
                'serviceName'   => $serviceName,
                'companyName'   => $companyName,
                'formattedDate' => $formattedDate,
                'formattedTime' => $formattedTime,
                'fullAddress'   => $fullAddress,
                'actionUrl'     => $actionUrl,
            ]);
    }
}
