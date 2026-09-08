@props(['url'])
@php
    $setting = \App\Models\Setting::first();
    $companyName = $setting?->company_name ?? config('app.name', 'Dust2Glow');
    $logoUrl = $setting?->company_logo_url ?? asset('public/assets/images/company_logo/brand_logo.png');
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ $logoUrl }}" class="logo" alt="{{ $companyName }} Logo" style="max-height: 50px; width: auto;">
</a>
</td>
</tr>
