@php
    $currentStep = $currentStep ?? 1;
    $steps = [
        1 => ['label' => 'Service Details', 'route' => 'booking-service.create'],
        2 => ['label' => 'Date & Time', 'route' => 'booking-service.date-time'],
        3 => ['label' => 'Your Details', 'route' => 'booking-service.your-details'],
        4 => ['label' => 'Review & Confirm', 'route' => 'booking-service.review-confirm'],
    ];
@endphp

<div class="booking-progress">
    @foreach ($steps as $stepNumber => $step)
        <a href="{{ route($step['route']) }}" class="booking-step {{ $stepNumber < $currentStep ? 'completed' : '' }} {{ $stepNumber === $currentStep ? 'active' : '' }}">
            <span class="booking-step-circle">
                @if ($stepNumber < $currentStep)
                    <i class="fas fa-check" aria-hidden="true"></i>
                @else
                    {{ $stepNumber }}
                @endif
            </span>
            <span class="booking-step-label">{{ $step['label'] }}</span>
        </a>
    @endforeach
</div>
