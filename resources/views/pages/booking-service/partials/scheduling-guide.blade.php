@php
    use App\Models\Setting;
    use Illuminate\Support\Facades\Cache;

    $appSettings = Cache::rememberForever('app_settings', function () {
        return Setting::latest()->first();
    });
    $cancellationNoticeHours = $appSettings?->cancellation_notice_hours ?? 24;
@endphp

<div class="booking-side-card scheduling-guide-card">
    <div class="scheduling-guide-header">
        <div class="header-title-group">
            <span class="header-calendar-icon">
                <i class="far fa-calendar-alt" aria-hidden="true"></i>
            </span>
            <h2 class="header-title">Scheduling Guide</h2>
        </div>
        <span class="step-pill-badge">Step 2 of 4</span>
    </div>

    <div class="scheduling-guide-intro">
        <p>Choose a time that works for you.</p>
        <p>Select from the available dates and times.</p>
        <p>Availability is updated based on our cleaning team's schedule.</p>
    </div>

    <hr class="scheduling-guide-divider">

    <div class="scheduling-guide-features">
        <div class="scheduling-feature-row">
            <div class="feature-icon-box">
                <i class="far fa-clock" aria-hidden="true"></i>
            </div>
            <div class="feature-content">
                <h3 class="feature-title">Flexible scheduling</h3>
                <p class="feature-desc">Choose a time that fits your routine.</p>
            </div>
        </div>

        <div class="scheduling-feature-row">
            <div class="feature-icon-box">
                <i class="fas fa-users" aria-hidden="true"></i>
            </div>
            <div class="feature-content">
                <h3 class="feature-title">Real-time availability</h3>
                <p class="feature-desc">Only available appointment times are shown.</p>
            </div>
        </div>

        <div class="scheduling-feature-row">
            <div class="feature-icon-box">
                <i class="fas fa-sync-alt" aria-hidden="true"></i>
            </div>
            <div class="feature-content">
                <h3 class="feature-title">Need to reschedule?</h3>
                <p class="feature-desc">You can reschedule your booking according to our policy.</p>
            </div>
        </div>
    </div>

    <hr class="scheduling-guide-divider">

    <div class="cancellation-policy-box">
        <div class="cancellation-shield-icon">
            <i class="fas fa-shield-alt" aria-hidden="true"></i>
        </div>
        <div class="cancellation-content">
            <h4 class="cancellation-title">Cancellation policy</h4>
            <p class="cancellation-desc">Free cancellation with at least <strong>{{ $cancellationNoticeHours }} {{ $cancellationNoticeHours == 1 ? "hour's" : "hours'" }} notice.</strong></p>
        </div>
    </div>
</div>
