@php
    $selectedService = null;
    if (!empty($selectedServiceId)) {
        $selectedService = $services->firstWhere('id', $selectedServiceId);
    }
@endphp

<div class="booking-side-card service-guide-card" id="service-guide-card">
    @if ($selectedService)
        <!-- State 2: Selected Service Redesign -->
        <div class="service-guide-header selected-service-header">
            <div class="service-guide-header-left">
                <span class="service-guide-icon-circle selected-service-icon">
                    <i class="fas fa-info" aria-hidden="true"></i>
                </span>
                <h2 class="service-guide-title selected-service-title">About {{ $selectedService->name }}</h2>
            </div>
            <span class="service-guide-badge">Step 1 of 4</span>
        </div>

        <p class="service-guide-description selected-service-description">
            {{ $selectedService->description }}
        </p>

        <hr class="service-guide-divider">

        <div class="whats-included-wrapper">
            <div class="whats-included-section">
                <h3 class="whats-included-heading">What's included</h3>
                @if (!empty($selectedService->whats_included) && is_array($selectedService->whats_included))
                    <ul class="whats-included-list selected-whats-included-list">
                        @foreach ($selectedService->whats_included as $item)
                            <li>
                                <i class="fas fa-check-circle whats-included-check-icon" aria-hidden="true"></i>
                                <span class="whats-included-text">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted small">No specific inclusions listed for this service.</p>
                @endif
            </div>

            <div class="selected-service-illustration">
                <svg width="150" height="140" viewBox="0 0 160 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background Circle / Backdrop -->
                    <circle cx="80" cy="75" r="65" fill="#F0F7FF"/>
                    
                    <!-- Clouds -->
                    <path d="M25 42C25 38.6863 27.6863 36 31 36C32.1 36 33.12 36.3 34 36.8C35.1 34.5 37.4 33 40 33C43.3 33 46 35.7 46 39C46.8 38.4 47.9 38 49 38C51.8 38 54 40.2 54 43C54 45.8 51.8 48 49 48H31C27.7 48 25 45.3 25 42Z" fill="#D6E4FF" opacity="0.7"/>
                    <path d="M110 32C110 29.2 112.2 27 115 27C115.9 27 116.8 27.2 117.5 27.7C118.4 25.8 120.3 24.5 122.5 24.5C125.3 24.5 127.5 26.7 127.5 29.5C128.2 29 129 28.7 130 28.7C132.3 28.7 134 30.4 134 32.7C134 35 132.3 36.7 130 36.7H115C112.2 36.7 110 34.5 110 32Z" fill="#D6E4FF" opacity="0.6"/>

                    <!-- Main Building Tower -->
                    <rect x="52" y="38" width="56" height="92" rx="4" fill="#0866E8"/>
                    <!-- Building Roof Structure -->
                    <rect x="62" y="30" width="36" height="8" rx="2" fill="#0052CC"/>
                    <rect x="76" y="22" width="8" height="8" fill="#0052CC"/>
                    
                    <!-- Secondary Annex Building -->
                    <rect x="36" y="68" width="22" height="62" rx="2" fill="#2563EB"/>
                    
                    <!-- Tower Windows Grid -->
                    <rect x="60" y="46" width="10" height="12" rx="1.5" fill="#E0F2FE"/>
                    <rect x="75" y="46" width="10" height="12" rx="1.5" fill="#E0F2FE"/>
                    <rect x="90" y="46" width="10" height="12" rx="1.5" fill="#E0F2FE"/>
                    
                    <rect x="60" y="64" width="10" height="12" rx="1.5" fill="#E0F2FE"/>
                    <rect x="75" y="64" width="10" height="12" rx="1.5" fill="#E0F2FE"/>
                    <rect x="90" y="64" width="10" height="12" rx="1.5" fill="#E0F2FE"/>
                    
                    <rect x="60" y="82" width="10" height="12" rx="1.5" fill="#E0F2FE"/>
                    <rect x="75" y="82" width="10" height="12" rx="1.5" fill="#E0F2FE"/>
                    <rect x="90" y="82" width="10" height="12" rx="1.5" fill="#E0F2FE"/>
                    
                    <!-- Entrance Glass Doors -->
                    <rect x="71" y="104" width="18" height="26" rx="2" fill="#93C5FD"/>
                    <line x1="80" y1="104" x2="80" y2="130" stroke="#0866E8" stroke-width="1.5"/>

                    <!-- Annex Building Windows -->
                    <rect x="42" y="76" width="10" height="10" rx="1" fill="#E0F2FE"/>
                    <rect x="42" y="92" width="10" height="10" rx="1" fill="#E0F2FE"/>
                    <rect x="42" y="108" width="10" height="10" rx="1" fill="#E0F2FE"/>

                    <!-- Ground / Base Strip -->
                    <rect x="20" y="128" width="120" height="6" rx="3" fill="#94A3B8"/>

                    <!-- Green Trees / Plants at Bottom -->
                    <circle cx="30" cy="116" r="11" fill="#10B981"/>
                    <circle cx="26" cy="118" r="8" fill="#059669"/>
                    <rect x="28" y="122" width="4" height="8" fill="#047857"/>

                    <circle cx="120" cy="114" r="13" fill="#10B981"/>
                    <circle cx="125" cy="116" r="9" fill="#059669"/>
                    <rect x="118" y="120" width="4" height="10" fill="#047857"/>

                    <!-- Sparkles -->
                    <path d="M48 30L49.5 34.5L54 36L49.5 37.5L48 42L46.5 37.5L42 36L46.5 34.5L48 30Z" fill="#F59E0B"/>
                    <path d="M106 20L107.5 24.5L112 26L107.5 27.5L106 32L104.5 27.5L100 26L104.5 24.5L106 20Z" fill="#F59E0B"/>
                </svg>
            </div>
        </div>

        <div class="service-guide-info-box selected-service-info-box">
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
                <div class="service-guide-header-text">
                    <h2 class="service-guide-title">Service Guide</h2>
                    <span class="service-guide-subtitle">Choose the right service for your home or business.</span>
                </div>
            </div>
            <span class="service-guide-badge">Step 1 of 4</span>
        </div>

        <hr class="service-guide-divider">

        <div class="service-guide-helper-box">
            <div class="helper-box-text">
                <h3 class="helper-box-heading">Not sure which cleaning service is right for you?</h3>
                <p class="helper-box-desc">Select a service on the left and we'll show you the relevant options and questions.</p>
            </div>
            <div class="helper-box-illustration">
                <svg width="140" height="120" viewBox="0 0 140 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="70" cy="65" r="55" fill="#EBF4FF"/>
                    <path d="M50 40H90C93.3137 40 96 42.6863 96 46V84C96 87.3137 93.3137 90 90 90H50C46.6863 90 44 87.3137 44 84V46C44 42.6863 46.6863 40 50 40Z" fill="#D6E4FF" stroke="#A4CAFE" stroke-width="2"/>
                    <path d="M54 48H86V72H54V48Z" fill="#FFFFFF"/>
                    <path d="M60 48L76 72" stroke="#B4D0FE" stroke-width="2" stroke-linecap="round"/>
                    <path d="M72 48L84 66" stroke="#B4D0FE" stroke-width="2" stroke-linecap="round"/>
                    <path d="M85 70L80 98H100L95 70H85Z" fill="#0866E8"/>
                    <rect x="83" y="65" width="14" height="6" rx="3" fill="#0052CC"/>
                    <path d="M90 65V55C90 52 94 52 94 55V65" stroke="#0052CC" stroke-width="3" stroke-linecap="round"/>
                    <path d="M40 35L42 41L48 43L42 45L40 51L38 45L32 43L38 41L40 35Z" fill="#F59E0B"/>
                    <path d="M102 30L103.5 34.5L108 36L103.5 37.5L102 42L100.5 37.5L96 36L100.5 34.5L102 30Z" fill="#60A5FA"/>
                    <path d="M35 75L36.5 79.5L41 81L36.5 82.5L35 87L33.5 82.5L29 81L33.5 79.5L35 75Z" fill="#10B981"/>
                </svg>
            </div>
        </div>

        <hr class="service-guide-divider">

        <div class="service-guide-features">
            <div class="service-guide-feature-row">
                <span class="feature-icon-circle">
                    <i class="fas fa-magic" aria-hidden="true"></i>
                </span>
                <div class="feature-row-content">
                    <h4 class="feature-row-title">Tailored to your needs</h4>
                    <p class="feature-row-desc">We customise each clean to fit your space and requirements.</p>
                </div>
            </div>

            <div class="service-guide-feature-row">
                <span class="feature-icon-circle">
                    <i class="fas fa-tag" aria-hidden="true"></i>
                </span>
                <div class="feature-row-content">
                    <h4 class="feature-row-title">Upfront pricing</h4>
                    <p class="feature-row-desc">Transparent pricing with no hidden costs.</p>
                </div>
            </div>

            <div class="service-guide-feature-row">
                <span class="feature-icon-circle">
                    <i class="fas fa-user-shield" aria-hidden="true"></i>
                </span>
                <div class="feature-row-content">
                    <h4 class="feature-row-title">Professional cleaners</h4>
                    <p class="feature-row-desc">Police-checked, trained, and committed to quality.</p>
                </div>
            </div>
        </div>
    @endif
</div>
