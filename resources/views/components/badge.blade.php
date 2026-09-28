@props([
    'variant' => 'primary',
    'outline' => false,
    'dismissible' => false,
])

@php
    $variantClass = $outline ? "badge-outline-{$variant}" : "bg-{$variant}";
@endphp

<span {{ $attributes->merge(['class' => "badge {$variantClass}"]) }}>
    {{ $slot }}
    @if($dismissible)
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</span>
