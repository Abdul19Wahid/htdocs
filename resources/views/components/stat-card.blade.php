@props([
    'title' => '',
    'value' => 0,
    'currency' => '$',
    'icon' => 'bi-wallet2',
    'variant' => 'primary',
    'trend' => null,
    'trendLabel' => '',
    'href' => null,
])

@php
    $variantClasses = match($variant) {
        'success' => 'bg-success-subtle text-success',
        'danger' => 'bg-danger-subtle text-danger',
        'warning' => 'bg-warning-subtle text-warning',
        'info' => 'bg-info-subtle text-info',
        default => 'bg-primary-subtle text-primary',
    };
    
    $cardClasses = $href ? 'cursor-pointer' : '';
@endphp

<div {{ $attributes->merge(['class' => "card stat-card p-4 h-100 border-0 shadow-sm {$cardClasses}"]) }}
     @if($href) onclick="window.location.href='{{ $href }}'" role="link" @endif>
    
    <div class="d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
            <div class="small-muted text-uppercase fw-semibold mb-2">{{ $title }}</div>
            
            @if($trend !== null)
                <div class="d-flex align-items-baseline gap-2">
                    <span class="fs-5 fw-bold text-amount">
                        {{ $currency }} {{ number_format($value, 2) }}
                    </span>
                    <span class="badge bg-{{ $trend > 0 ? 'success' : 'danger' }} ms-auto">
                        <i class="bi bi-{{ $trend > 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                        {{ abs($trend) }}%
                    </span>
                </div>
            @else
                <div class="fs-5 fw-bold text-amount">
                    {{ $currency }} {{ number_format($value, 2) }}
                </div>
            @endif
        </div>
        
        <div class="stat-icon {{ $variantClasses }}">
            <i class="bi {{ $icon }} fs-5"></i>
        </div>
    </div>
    
    @if($trendLabel)
        <div class="mt-3 pt-3 border-top border-opacity-25">
            <small class="text-muted">{{ $trendLabel }}</small>
        </div>
    @endif
</div>
