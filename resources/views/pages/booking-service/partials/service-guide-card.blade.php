@php
    $selectedService = null;
    if (!empty($selectedServiceId)) {
        $selectedService = $services->firstWhere('id', $selectedServiceId);
    }
@endphp

<div class="booking-side-card service-guide-card" id="service-guide-card">
    @if ($selectedService)
        <!-- State 2: Service Selected -->
        <div class="service-guide-header">
            <div class="service-guide-header-left">
                <span class="service-guide-icon-circle">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                </span>
                <h2 class="service-guide-title">About {{ $selectedService->name }}</h2>
            </div>
            <span class="service-guide-badge">Step 1 of 4</span>
        </div>

        <p class="service-guide-description">
            {{ $selectedService->description }}
        </p>

        <hr class="service-guide-divider">

        <div class="service-guide-content-area">
            <div class="whats-included-section">
                <h3 class="whats-included-heading">What's included</h3>
                @if (!empty($selectedService->whats_included) && is_array($selectedService->whats_included))
                    <ul class="whats-included-list">
                        @foreach ($selectedService->whats_included as $item)
                            <li>
                                <span class="check-icon-circle">
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                </span>
                                <span class="whats-included-text">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted small">No specific inclusions listed for this service.</p>
                @endif
            </div>

            <div class="service-guide-illustration">
                <svg width="120" height="110" viewBox="0 0 140 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="70" cy="65" r="55" fill="#F0F7FF"/>
                    <path d="M50 40H90C93.3137 40 96 42.6863 96 46V84C96 87.3137 93.3137 90 90 90H50C46.6863 90 44 87.3137 44 84V46C44 42.6863 46.6863 40 50 40Z" fill="#E1EFFE" stroke="#A4CAFE" stroke-width="2"/>
                    <path d="M54 48H86V72H54V48Z" fill="#FFFFFF"/>
                    <path d="M60 48L76 72" stroke="#D0E1FD" stroke-width="2" stroke-linecap="round"/>
                    <path d="M72 48L84 66" stroke="#D0E1FD" stroke-width="2" stroke-linecap="round"/>
                    <path d="M85 70L80 98H100L95 70H85Z" fill="#0866E8"/>
                    <rect x="83" y="65" width="14" height="6" rx="3" fill="#0052CC"/>
                    <path d="M90 65V55C90 52 94 52 94 55V65" stroke="#0052CC" stroke-width="3" stroke-linecap="round"/>
                    <path d="M40 35L42 41L48 43L42 45L40 51L38 45L32 43L38 41L40 35Z" fill="#F59E0B"/>
                    <path d="M102 30L103.5 34.5L108 36L103.5 37.5L102 42L100.5 37.5L96 36L100.5 34.5L102 30Z" fill="#60A5FA"/>
                    <path d="M35 75L36.5 79.5L41 81L36.5 82.5L35 87L33.5 82.5L29 81L33.5 79.5L35 75Z" fill="#10B981"/>
                </svg>
            </div>
        </div>

        <div class="service-guide-info-box">
            <span class="info-box-shield-icon">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
            </span>
            <span>Customised cleaning plans available to suit your business needs.</span>
        </div>
    @else
        <!-- State 1: Default / Initial Page Load -->
        <div class="service-guide-header">
            <div class="service-guide-header-left">
                <span class="service-guide-icon-circle">
                    <i class="far fa-lightbulb" aria-hidden="true"></i>
                </span>
                <h2 class="service-guide-title">Service Guide</h2>
            </div>
            <span class="service-guide-badge">Step 1 of 4</span>
        </div>

        <div class="service-guide-default-intro">
            <h3>Not sure which cleaning service is right for you?</h3>
            <p>Select a service from the dropdown to view details, what's included, and package coverage.</p>
        </div>

        <hr class="service-guide-divider">

        <div class="service-guide-content-area">
            <div class="whats-included-section">
                <ul class="whats-included-list default-features-list">
                    <li>
                        <span class="check-icon-circle">
                            <i class="fas fa-check" aria-hidden="true"></i>
                        </span>
                        <span class="whats-included-text">Tailored to your needs</span>
                    </li>
                    <li>
                        <span class="check-icon-circle">
                            <i class="fas fa-check" aria-hidden="true"></i>
                        </span>
                        <span class="whats-included-text">Upfront pricing</span>
                    </li>
                    <li>
                        <span class="check-icon-circle">
                            <i class="fas fa-check" aria-hidden="true"></i>
                        </span>
                        <span class="whats-included-text">Professional cleaners</span>
                    </li>
                </ul>
            </div>

            <div class="service-guide-illustration">
                <svg width="120" height="110" viewBox="0 0 140 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="70" cy="65" r="55" fill="#F0F7FF"/>
                    <path d="M50 40H90C93.3137 40 96 42.6863 96 46V84C96 87.3137 93.3137 90 90 90H50C46.6863 90 44 87.3137 44 84V46C44 42.6863 46.6863 40 50 40Z" fill="#E1EFFE" stroke="#A4CAFE" stroke-width="2"/>
                    <path d="M54 48H86V72H54V48Z" fill="#FFFFFF"/>
                    <path d="M60 48L76 72" stroke="#D0E1FD" stroke-width="2" stroke-linecap="round"/>
                    <path d="M72 48L84 66" stroke="#D0E1FD" stroke-width="2" stroke-linecap="round"/>
                    <path d="M85 70L80 98H100L95 70H85Z" fill="#0866E8"/>
                    <rect x="83" y="65" width="14" height="6" rx="3" fill="#0052CC"/>
                    <path d="M90 65V55C90 52 94 52 94 55V65" stroke="#0052CC" stroke-width="3" stroke-linecap="round"/>
                    <path d="M40 35L42 41L48 43L42 45L40 51L38 45L32 43L38 41L40 35Z" fill="#F59E0B"/>
                    <path d="M102 30L103.5 34.5L108 36L103.5 37.5L102 42L100.5 37.5L96 36L100.5 34.5L102 30Z" fill="#60A5FA"/>
                    <path d="M35 75L36.5 79.5L41 81L36.5 82.5L35 87L33.5 82.5L29 81L33.5 79.5L35 75Z" fill="#10B981"/>
                </svg>
            </div>
        </div>

        <div class="service-guide-info-box">
            <span class="info-box-shield-icon">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
            </span>
            <span>Customised cleaning plans available to suit your business needs.</span>
        </div>
    @endif
</div>
