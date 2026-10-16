<?php

namespace App\Http\Controllers\Traits;

use App\Models\Booking;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

trait DownloadsBookingInvoicePdf
{
    protected function downloadBookingInvoicePdf(Booking $booking)
    {
        $booking->loadMissing(['user', 'service', 'payment', 'images']);
        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.booking-invoice', compact('booking', 'setting'))
            ->setPaper('a4', 'portrait');

        $fileName = '#BK-' . sprintf('%03d', $booking->id) . '.pdf';

        return $pdf->download($fileName);
    }
}
