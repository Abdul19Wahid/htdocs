@props([
    'type' => 'info', // info, success, warning, danger
    'dismissible' => true,
    'icon' => null,
    'title' => null,
])

@php
    $typeClasses = match($type) {
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'danger' => 'alert-danger',
        default => 'alert-info',
    };
    
    $defaultIcon = match($type) {
        'success' => 'bi-check-circle',
        'warning' => 'bi-exclamation-triangle',
        'danger' => 'bi-exclamation-octagon',
        default => 'bi-info-circle',
    };
    
    $displayIcon = $icon ?? $defaultIcon;
@endphp

<div class="alert {{ $typeClasses }} {{ $dismissible ? 'alert-dismissible fade show' : '' }} alert-animated" 
     role="alert"
     {{ $attributes->merge(['class' => '']) }}>
    
    @if($title || $displayIcon)
        <div class="d-flex align-items-start gap-2">
            @if($displayIcon)
                <i class="bi {{ $displayIcon }} flex-shrink-0 mt-0.5"></i>
            @endif
            <div class="flex-grow-1">
                @if($title)
                    <strong>{{ $title }}</strong>
                @endif
                {{ $slot }}
            </div>
            @if($dismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            @endif
        </div>
    @else
        {{ $slot }}
        @if($dismissible)
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        @endif
    @endif
</div>

<style>
    .alert-animated {
        animation: slideInUp 0.4s ease-out;
    }
</style>
