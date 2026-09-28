@props([
    'title' => null,
    'subtitle' => null,
    'footer' => null,
    'headerAction' => null,
    'shadow' => true,
    'noPadding' => false,
])

@php
    $shadowClass = $shadow ? 'shadow-sm' : '';
    $paddingClass = $noPadding ? '' : 'p-4';
@endphp

<div {{ $attributes->merge(['class' => "card {$shadowClass} border-0"]) }}>
    @if($title)
        <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">{{ $title }}</h5>
                @if($subtitle)
                    <p class="text-muted small mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            @if($headerAction)
                <div>{{ $headerAction }}</div>
            @endif
        </div>
    @endif
    
    <div class="{{ $paddingClass }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="card-footer border-0 bg-transparent p-4">
            {{ $footer }}
        </div>
    @endif
</div>
