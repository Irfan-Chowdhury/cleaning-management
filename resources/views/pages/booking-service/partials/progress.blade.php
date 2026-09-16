@php
    $currentStep = $currentStep ?? 1;
    $steps = [
        1 => ['label' => 'Service Details', 'route' => 'booking-service.create', 'params' => []],
        2 => ['label' => 'Date & Time', 'route' => 'booking-service.date-time', 'params' => []],
        3 => ['label' => 'Your Details', 'route' => 'booking-service.your-details', 'params' => []],
    ];

    if ($currentStep === 4) {
        $bookingParam = request()->query('booking') ?? request()->query('booking_id') ?? ($latestBooking->id ?? null);
        $steps[4] = [
            'label' => 'Review & Confirm',
            'route' => 'booking-service.review-confirm',
            'params' => $bookingParam ? ['booking' => $bookingParam] : [],
        ];
    }
@endphp

<div class="booking-progress">
    @foreach ($steps as $stepNumber => $step)
        @php
            $isDisabled = ($currentStep === 4 && $stepNumber < 4);
        @endphp

        @if ($isDisabled)
            <span class="booking-step {{ $stepNumber < $currentStep ? 'completed' : '' }} {{ $stepNumber === $currentStep ? 'active' : '' }} disabled">
                <span class="booking-step-circle">
                    @if ($stepNumber < $currentStep)
                        <i class="fas fa-check" aria-hidden="true"></i>
                    @else
                        {{ $stepNumber }}
                    @endif
                </span>
                <span class="booking-step-label">{{ $step['label'] }}</span>
            </span>
        @else
            <a href="{{ route($step['route'], $step['params'] ?? []) }}" class="booking-step {{ $stepNumber < $currentStep ? 'completed' : '' }} {{ $stepNumber === $currentStep ? 'active' : '' }}">
                <span class="booking-step-circle">
                    @if ($stepNumber < $currentStep)
                        <i class="fas fa-check" aria-hidden="true"></i>
                    @else
                        {{ $stepNumber }}
                    @endif
                </span>
                <span class="booking-step-label">{{ $step['label'] }}</span>
            </a>
        @endif
    @endforeach
</div>
