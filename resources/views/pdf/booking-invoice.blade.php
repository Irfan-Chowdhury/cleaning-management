<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - BK-{{ sprintf('%03d', $booking->id) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            font-size: 13px;
            line-height: 1.5;
            padding: 30px;
        }

        .invoice-box {
            width: 100%;
            margin: auto;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .company-logo-img {
            max-height: 55px;
            width: auto;
            margin-bottom: 8px;
            display: block;
        }

        .company-name {
            font-size: 22px;
            font-weight: 800;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .company-sub {
            font-size: 11px;
            color: #64748b;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-meta {
            text-align: right;
            font-size: 12px;
            color: #475569;
            margin-top: 4px;
        }

        .badge-status {
            display: inline-block;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 20px;
            margin-top: 6px;
        }

        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* Divider */
        .divider {
            height: 2px;
            background-color: #2563eb;
            margin-bottom: 25px;
        }

        /* Two Column Info Section */
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .info-table td {
            width: 50%;
            vertical-align: top;
            padding: 12px 16px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .section-heading {
            font-size: 12px;
            font-weight: 700;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }

        .info-list p {
            margin-bottom: 4px;
            font-size: 12px;
            color: #334155;
        }

        .info-list strong {
            color: #0f172a;
        }

        /* Itemized Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            text-align: left;
        }

        .data-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12.5px;
            color: #334155;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Price Breakdown Table */
        .summary-wrapper {
            width: 100%;
            margin-bottom: 30px;
        }

        .summary-table {
            width: 320px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 6px 12px;
            font-size: 12.5px;
            color: #475569;
        }

        .summary-table td.amount-col {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }

        .summary-table tr.total-row td {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            border-top: 2px solid #2563eb;
            border-bottom: 2px solid #2563eb;
            padding: 10px 12px;
            background-color: #eff6ff;
        }

        .summary-table tr.total-row td.amount-col {
            color: #2563eb;
        }

        .text-success {
            color: #16a34a !important;
        }

        /* Footer Notes */
        .footer-note {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 11px;
            color: #64748b;
        }

        .footer-note strong {
            color: #0f172a;
        }
    </style>
</head>
<body>
    @php
        $companyName = $setting?->company_name ?: 'Dust2Glow Cleaning Services';
        $companyEmail = $setting?->email ?: 'support@dust2glow.com';
        $companyPhone = $setting?->phone ?: '+61 (0) 400 000 000';
        $companyAddress = $setting?->address ?: 'Sydney, NSW, Australia';

        // Base64 Logo Encoding
        $logoPath = null;
        if (!empty($setting?->company_logo)) {
            if (file_exists(public_path('assets/images/company_logo/' . $setting->company_logo))) {
                $logoPath = public_path('assets/images/company_logo/' . $setting->company_logo);
            } elseif (file_exists(public_path($setting->company_logo))) {
                $logoPath = public_path($setting->company_logo);
            }
        }
        if (!$logoPath && file_exists(public_path('assets/images/company_logo/brand_logo.png'))) {
            $logoPath = public_path('assets/images/company_logo/brand_logo.png');
        }

        $logoBase64 = null;
        if ($logoPath && file_exists($logoPath)) {
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $bookingIdFormatted = 'BK-' . sprintf('%03d', $booking->id);
        $bookingDateFormatted = $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y (D)') : 'N/A';
        $bookingTimeFormatted = $booking->start_time
            ? (\Carbon\Carbon::parse($booking->start_time)->format('g:i A') . ($booking->end_time ? ' - ' . \Carbon\Carbon::parse($booking->end_time)->format('g:i A') : ''))
            : 'N/A';

        $subtotal = (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount);
        $discountAmount = (float) ($booking->discount_amount ?? 0);
        $creditUsed = (float) ($booking->credit_used ?? 0);
        $totalAmount = (float) ($booking->total_amount ?? 0);

        $paymentStatus = strtolower((string) ($booking->payment_status ?? 'pending'));
        $isPaid = ($paymentStatus === 'paid');
    @endphp

    <div class="invoice-box">
        <!-- Header Table -->
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    @if (!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" class="company-logo-img" alt="Company Logo">
                    @endif
                    <div class="company-sub">{{ $companyAddress }}</div>
                    <div class="company-sub">Phone: {{ $companyPhone }} | Email: {{ $companyEmail }}</div>
                </td>
                <td style="width: 45%; text-align: right;">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta"><strong>Invoice #:</strong> {{ $bookingIdFormatted }}</div>
                    <div class="invoice-meta"><strong>Date:</strong> {{ $booking->created_at ? $booking->created_at->format('d M Y') : date('d M Y') }}</div>
                    <div class="invoice-meta">
                        <span class="badge-status {{ $isPaid ? 'badge-paid' : 'badge-pending' }}">
                            {{ $isPaid ? 'Payment Paid' : 'Payment Pending' }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Info Section -->
        <table class="info-table">
            <tr>
                <td style="margin-right: 10px;">
                    <div class="section-heading">Customer Information</div>
                    <div class="info-list">
                        <p><strong>Name:</strong> {{ $booking->customer_name ?: ($booking->user ? trim($booking->user->first_name . ' ' . ($booking->user->last_name ?? '')) : 'Guest') }}</p>
                        <p><strong>Email:</strong> {{ $booking->customer_email ?: ($booking->user->email ?? 'N/A') }}</p>
                        <p><strong>Phone:</strong> {{ $booking->customer_phone ?: ($booking->user->phone ?? 'N/A') }}</p>
                        <p><strong>Address:</strong> {{ $booking->customer_address ?: 'N/A' }}</p>
                        @if ($booking->unit_suite_floor)
                            <p><strong>Unit / Suite / Floor:</strong> {{ $booking->unit_suite_floor }}</p>
                        @endif
                        @if ($booking->suburb || $booking->postcode)
                            <p><strong>Suburb / Postcode:</strong> {{ $booking->suburb ?? '' }} {{ $booking->postcode ?? '' }}</p>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="section-heading">Service &amp; Schedule</div>
                    <div class="info-list">
                        <p><strong>Service:</strong> {{ $booking->service?->name ?? 'Cleaning Service' }}</p>
                        <p><strong>Scheduled Date:</strong> {{ $bookingDateFormatted }}</p>
                        <p><strong>Time Slot:</strong> {{ $bookingTimeFormatted }}</p>
                        <p><strong>Booking Status:</strong> {{ ucfirst($booking->status_label ?? 'Pending') }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Itemized Service Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Scheduled Time</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $booking->service?->name ?? 'Professional Cleaning Service' }}</strong>
                        @if ($booking->special_instructions)
                            <br><small style="color: #64748b;">Notes: {{ $booking->special_instructions }}</small>
                        @endif
                    </td>
                    <td>{{ $bookingDateFormatted }} ({{ $bookingTimeFormatted }})</td>
                    <td style="text-align: right;">${{ number_format($subtotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Service Questionnaire Section -->
        @if (!empty($booking->answers) && is_array($booking->answers))
            <div style="margin-top: 20px; margin-bottom: 25px;">
                <div class="section-heading" style="font-size: 12px; font-weight: 700; color: #2563eb; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px;">
                    Service Questionnaire &amp; Details
                </div>
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 40%; background-color: #1e293b; color: #ffffff; padding: 8px 12px; font-size: 11px;">Question</th>
                            <th style="width: 60%; background-color: #1e293b; color: #ffffff; padding: 8px 12px; font-size: 11px;">Answer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($booking->answers as $ans)
                            @if (!empty($ans['question']) && !empty($ans['answer']))
                                <tr>
                                    <td style="padding: 8px 12px; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: 12px; color: #0f172a;">{{ $ans['question'] }}</td>
                                    <td style="padding: 8px 12px; border-bottom: 1px solid #e2e8f0; font-size: 12px; color: #334155;">{{ $ans['answer'] }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Space Photos / Uploaded Images Section -->
        {{--
            @if (!empty($booking->images) && $booking->images->count() > 0)
                <div style="margin-top: 20px; margin-bottom: 25px;">
                    <div class="section-heading" style="font-size: 12px; font-weight: 700; color: #2563eb; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px;">
                        Space Photos / Uploaded Images ({{ $booking->images->count() }})
                    </div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            @foreach ($booking->images as $index => $img)
                                @php
                                    $rawImgPath = $img->image_path;
                                    $fullPath = public_path('public/' . $rawImgPath);

                                    if (!file_exists($fullPath)) {
                                        $fullPath = public_path($rawImgPath);
                                    }

                                    $imgBase64 = null;

                                    if (file_exists($fullPath) && is_file($fullPath)) {
                                        $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                                        $imgBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
                                    }
                                @endphp

                                @if ($imgBase64)
                                    <td style="width: 33.33%; padding: 6px; text-align: center; vertical-align: top;">
                                        <div style="border: 1px solid #cbd5e1; padding: 4px; background: #ffffff; border-radius: 6px;">
                                            <img src="{{ $imgBase64 }}"
                                                style="max-width: 100%; max-height: 120px; object-fit: cover; border-radius: 4px;"
                                                alt="Uploaded Photo">
                                        </div>
                                    </td>

                                    @if (($index + 1) % 3 === 0 && !$loop->last)
                                        </tr><tr>
                                    @endif
                                @endif
                            @endforeach
                        </tr>
                    </table>
                </div>
            @endif
        --}}

        <!-- Summary & Totals -->
        <div class="summary-wrapper">
            <table class="summary-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="amount-col">${{ number_format($subtotal, 2) }}</td>
                </tr>
                @if ($booking->referal_code || $booking->promo_code)
                    <tr>
                        <td>Discount ({{ $booking->referal_code ? 'Referral Code: ' . $booking->referal_code : 'Promo: ' . $booking->promo_code }}):</td>
                        <td class="amount-col text-success">- ${{ number_format($discountAmount, 2) }}</td>
                    </tr>
                @elseif ($discountAmount > 0 && $creditUsed <= 0)
                    <tr>
                        <td>Discount Offer:</td>
                        <td class="amount-col text-success">- ${{ number_format($discountAmount, 2) }}</td>
                    </tr>
                @endif

                @if ($creditUsed > 0)
                    <tr>
                        <td>Wallet Credit Used:</td>
                        <td class="amount-col text-success">- ${{ number_format($creditUsed, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Total {{ $isPaid ? 'Paid' : 'Amount Due' }}:</td>
                    <td class="amount-col">${{ number_format($totalAmount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            <p>Thank you for booking with <strong>{{ $companyName }}</strong>!</p>
            <p>For any inquiries regarding this invoice, please contact {{ $companyEmail }} or call {{ $companyPhone }}.</p>
        </div>
    </div>
</body>
</html>
