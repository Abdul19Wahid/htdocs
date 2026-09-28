@props([
    'value' => 0,
    'max' => 100,
    'label' => null,
    'variant' => 'primary',
    'striped' => false,
    'animated' => false,
    'showPercent' => true,
])

@php
    $percent = min(100, ($value / $max) * 100);
    $variantClass = "bg-{$variant}";
    $classes = [$variantClass];
    if ($striped) $classes[] = 'progress-bar-striped';
    if ($animated) $classes[] = 'progress-bar-animated';
@endphp

<div class="mb-2">
    @if($label)
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="fw-semibold small">{{ $label }}</label>
            @if($showPercent)
                <span class="badge bg-{{ $variant }}">{{ round($percent) }}%</span>
            @endif
        </div>
    @endif
    
    <div class="progress" style="height: 8px;">
        <div class="{{ implode(' ', $classes) }}" 
             role="progressbar" 
             style="width: {{ $percent }}%"
             aria-valuenow="{{ $value }}" 
             aria-valuemin="0" 
             aria-valuemax="{{ $max }}">
        </div>
    </div>
</div>
