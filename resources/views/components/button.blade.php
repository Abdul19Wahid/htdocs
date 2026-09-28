@props([
    'variant' => 'primary', // primary, success, warning, danger, secondary
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'href' => null,
    'disabled' => false,
    'loading' => false,
])

@php
    $variants = [
        'primary' => 'btn-primary',
        'success' => 'btn-success',
        'warning' => 'btn-warning',
        'danger' => 'btn-danger',
        'secondary' => 'btn-secondary',
        'light' => 'btn-light',
        'outline-primary' => 'btn-outline-primary',
    ];
    
    $sizes = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];
    
    $variantClass = $variants[$variant] ?? 'btn-primary';
    $sizeClass = $sizes[$size] ?? '';
    
    $classes = "btn {$variantClass} {$sizeClass}";
    
    if ($href) {
        return;
    }
@endphp

@if($href)
    <a href="{{ $href }}" 
       {{ $attributes->merge(['class' => $classes]) }}
       @if($disabled) aria-disabled="true" @endif>
        @if($icon)
            <i class="bi {{ $icon }} me-2"></i>
        @endif
        @if($loading)
            <span class="spinner-border spinner-border-sm me-2"></span>
        @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}
            @if($disabled) disabled @endif
            @if($loading) aria-busy="true" @endif>
        @if($icon)
            <i class="bi {{ $icon }} me-2"></i>
        @endif
        @if($loading)
            <span class="spinner-border spinner-border-sm me-2"></span>
        @endif
        {{ $slot }}
    </button>
@endif
