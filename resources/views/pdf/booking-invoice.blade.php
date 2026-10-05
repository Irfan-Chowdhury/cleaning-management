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

        .company-name {
            font-size: 24px;
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

        $bookingIdFormatted = 'BK-' . sprintf('%03d', $booking->id);
        $bookingDateFormatted = $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y (D)') : 'N/A';
        $bookingTimeFormatted = $booking->start_time
            ? (\Carbon\Carbon::parse($booking->start_time)->format('g:i A') . ($booking->end_time ? ' - ' . \Carbon\Carbon::parse($booking->end_time)->format('g:i A') : ''))
            : 'N/A';
        $frequencyLabel = ucfirst(str_replace('_', ' ', $booking->frequency ?? 'one_time'));

        $subtotal = (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount);
        $discountAmount = (float) ($booking->discount_amount ?? 0);
        $creditUsed = (float) ($booking->credit_used ?? 0);
        $totalAmount = (float) ($booking->total_amount ?? 0);

        $paymentStatus = strtolower((string) ($booking->payment_status ?? 'pending'));
        $isPaid = ($paymentStatus === 'paid');
    @endphp

    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="company-name">{{ $companyName }}</div>
                    <div class="company-sub">{{ $companyAddress }}</div>
                    <div class="company-sub">Phone: {{ $companyPhone }} | Email: {{ $companyEmail }}</div>
                </td>
                <td>
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
                        <p><strong>Frequency:</strong> {{ $frequencyLabel }}</p>
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
                    <th>Frequency</th>
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
                    <td>{{ $frequencyLabel }}</td>
                    <td>{{ $bookingDateFormatted }} ({{ $bookingTimeFormatted }})</td>
                    <td style="text-align: right;">${{ number_format($subtotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

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
