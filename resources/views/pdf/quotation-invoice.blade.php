<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle }} - {{ $quotation->quote_number }}</title>
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
            font-size: 11.5px;
            line-height: 1.45;
            padding: 28px 36px 28px 36px;
        }

        .invoice-box {
            width: 100%;
            margin: auto;
        }

        /* Header */
        .header-table {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }

        .company-logo-img {
            max-height: 52px;
            width: auto;
            margin-bottom: 6px;
            display: block;
        }

        .company-name {
            font-size: 12px;
            font-weight: 700;
            color: #010711;
            margin-bottom: 4px;
        }

        .company-sub {
            font-size: 10.5px;
            color: #64748b;
            line-height: 1.35;
        }

        /* Quote Title Box */
        .quote-title-box {
            text-align: right;
        }

        .quote-title {
            font-size: 30px;
            font-weight: 900;
            color: #0052cc;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .quote-number {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            display: inline-block;
            padding: 3px 10px;
        }

        .quote-date {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 2px solid #0052cc;
            margin: 14px 0 16px;
        }

        .divider-light {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 12px 0;
        }

        /* Prepared-For / Service Details */
        .info-section-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .info-section-table td {
            vertical-align: top;
            padding: 0 6px 0 0;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 10px 14px;
        }

        .info-box-title {
            font-size: 9px;
            font-weight: 800;
            color: #0052cc;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 7px;
            border-bottom: 1px solid #dbeafe;
            padding-bottom: 4px;
        }

        .info-row {
            margin-bottom: 4px;
            font-size: 11px;
        }

        .info-label {
            font-weight: 700;
            color: #475569;
            display: inline-block;
            min-width: 80px;
        }

        .info-value {
            color: #1e293b;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .items-table thead tr {
            background-color: #0052cc;
        }

        .items-table thead th {
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 8px 12px;
            text-align: left;
            border: none;
        }

        .items-table thead th.text-right {
            text-align: right;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .items-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .items-table tbody td {
            padding: 8px 12px;
            font-size: 11px;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .items-table tbody td.text-right {
            text-align: right;
            font-weight: 600;
        }

        .items-table tbody td.idx {
            text-align: center;
            color: #94a3b8;
            font-weight: 700;
            width: 36px;
        }

        /* Totals */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .totals-table td {
            padding: 5px 12px;
            font-size: 11.5px;
            border: none;
        }

        .totals-table .label-col {
            text-align: right;
            color: #000711;
            font-weight: 800;
        }

        .totals-table .value-col {
            text-align: right;
            font-weight: 700;
            color: #1e293b;
            width: 110px;
        }

        .totals-table .total-row td {
            background-color: #eff6ff;
            border-top: 2px solid #0052cc;
            font-size: 13px;
            font-weight: 800;
            color: #0052cc;
            padding: 8px 12px;
        }

        .bottom-table {
            width: 100%;
            margin-top: 18px;
            border-collapse: collapse;
        }

        .bottom-table td {
            vertical-align: top;
        }

        .bottom-cell {
            width: 48.5%;
            padding: 0;
        }

        .bottom-spacer {
            width: 3%;
        }

        .card-box {
            min-height: 180px;
            border: 1px solid #0052cc;
            border-radius: 5px;
            overflow: hidden;
            background: #ffffff;
        }

        .block-header {
            padding: 7px 12px;
            background: #0052cc;
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .block-content {
            padding: 8px 12px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payment-table td {
            padding: 4px 0;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
            line-height: 1.35;
        }

        .payment-table tr:last-child td {
            border-bottom: none;
        }

        .payment-table td:first-child {
            width: 38%;
            color: #0f172a;
            font-weight: 700;
        }

        .terms-list {
            margin: 0;
            padding-left: 14px;
            color: #2563eb;
            font-size: 9.5px;
            line-height: 1.3;
        }

        .terms-list li {
            margin-bottom: 3px;
        }

        .terms-list li span {
            color: #334155;
        }

        /* Footer */
        .footer {
            margin-top: 22px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            text-align: center;
        }

        .footer-title {
            margin-bottom: 4px;
            font-size: 18px;
            font-weight: 800;
            color: #0052cc;
        }

        .footer-message {
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>
<div class="invoice-box">

    {{-- ── HEADER ── --}}
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                @if ($setting?->company_logo)
                    <img class="company-logo-img"
                         src="{{ public_path($setting->company_logo) }}"
                         alt="{{ $setting->company_name ?? 'Company' }}">
                @endif
                @if ($setting?->address)
                    <div class="company-sub">{{ $setting->address }}</div>
                @endif
                @if ($setting?->phone)
                    <div class="company-sub">Ph: {{ $setting->phone }}</div>
                @endif
                @if ($setting?->email)
                    <div class="company-sub">{{ $setting->email }}</div>
                @endif
                @if ($setting?->abn)
                    <div class="company-sub">ABN: {{ $setting->abn }}</div>
                @endif
            </td>
            <td class="quote-title-box">
                <div class="quote-title">{{ $documentTitle }}</div>
                <div class="company-name">{{ ucfirst((string) ($setting?->company_name ?? config('app.name'))) }}</div>
                <div class="quote-number">{{ $quotation->quote_number }}</div>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ── PREPARED FOR + SERVICE DETAILS ── --}}
    <table class="info-section-table">
        <tr>
            <td style="width: 60%; padding-right: 12px;">
                <div class="info-box">
                    <div class="info-box-title">Prepared For</div>
                    <div class="info-row">
                        <span class="info-label">Company:</span>
                        <span class="info-value">{{ $quotation->company_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact:</span>
                        <span class="info-value">{{ $quotation->contact_person }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value">{{ $quotation->address }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $quotation->phone }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $quotation->email }}</span>
                    </div>
                    @if ($quotation->abn)
                        <div class="info-row">
                            <span class="info-label">ABN:</span>
                            <span class="info-value">{{ $quotation->abn }}</span>
                        </div>
                    @endif
                </div>
            </td>
            <td style="width: 40%;">
                <div class="info-box">
                    <div class="info-box-title">Invoice Details</div>
                    <div class="info-row">
                        <span class="info-label">Invoice No:</span>
                        <span class="info-value">{{ $quotation->quote_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Invoice Date:</span>
                        <span class="info-value">{{ $quoteDateFormatted }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Due Date:</span>
                        <span class="info-value">{{ $dueDateFormatted }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Frequency:</span>
                        <span class="info-value">{{ $quotation->frequency }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Schedule:</span>
                        <span class="info-value">{{ $quotation->shift_schedule }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── SERVICE ITEMS ── --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 36px; text-align: center;">#</th>
                <th>Description</th>
                <th class="text-right" style="width: 120px;">Amount (ex GST)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $index => $item)
                <tr>
                    <td class="idx">{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">${{ number_format((float) $item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── TOTALS ── --}}
    <table class="totals-table">
        <tr>
            <td class="label-col">Subtotal:</td>
            <td class="value-col">${{ number_format((float) $quotation->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label-col">GST (10%):</td>
            <td class="value-col">${{ number_format((float) $quotation->gst_amount, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td class="label-col" style="color: #0052cc; font-weight: 800;">Total (inc. GST):</td>
            <td class="value-col" style="color: #0052cc; font-weight: 800;">
                ${{ number_format((float) $quotation->total_amount, 2) }}
            </td>
        </tr>
    </table>

    <table class="bottom-table">
        <tr>
            <td class="bottom-cell">
                <div class="card-box">
                    <div class="block-header">Payment Details</div>
                    <div class="block-content">
                        <table class="payment-table">
                            <tr><td>Bank:</td><td>Commonwealth Bank</td></tr>
                            <tr><td>Account Name:</td><td>Dust2Glow Group Pty Ltd</td></tr>
                            <tr><td>BSB:</td><td>062 709</td></tr>
                            <tr><td>Account Number:</td><td>10 450 457</td></tr>
                            <tr><td>Reference:</td><td>{{ $quotation->quote_number }}</td></tr>
                        </table>
                    </div>
                </div>
            </td>
            <td class="bottom-spacer"></td>
            <td class="bottom-cell">
                <div class="card-box">
                    <div class="block-header">Terms &amp; Notes</div>
                    <div class="block-content">
                        <ul class="terms-list">
                            <li><span>Cleaning is carried out overnight, with a start time between 12am and 6am each week, subject to site access being arranged.</span></li>
                            <li><span>All cleaning products and equipment are supplied by Dust2Glow.</span></li>
                            <li><span>Prices are per visit and cover the scope above only. Additional work is quoted separately.</span></li>
                            <li><span>Late Payment Terms: Payment is due within 7 days of the invoice date. Any amount remaining unpaid after 14 days from the invoice date will incur interest at 8.35% per annum (the current NSW court pre-judgment interest rate), calculated daily as simple interest on the outstanding balance until paid in full.</span></li>
                            <li><span>For any questions, please contact us at info@dust2glowcleaning.com.au or call 0426 003 944.</span></li>
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-title">Thank you for choosing Dust2Glow</div>
        <div class="footer-message">We look forward to working with you.</div>
    </div>

</div>
</body>
</html>
