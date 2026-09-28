@props([
    'label' => null,
    'help' => null,
    'error' => null,
    'required' => false,
])

@php
    $hasError = $error || $errors->has($attributes->get('name'));
    $errorMessage = $error ?? ($hasError ? $errors->first($attributes->get('name')) : null);
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $attributes->get('id') ?? $attributes->get('name') }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger" aria-label="required">*</span>
            @endif
        </label>
    @endif
    
    <div>
        {{ $slot }}
        
        @if($errorMessage)
            <div class="invalid-feedback d-block">
                <i class="bi bi-exclamation-circle me-1"></i>
                {{ $errorMessage }}
            </div>
        @endif
        
        @if($help && !$errorMessage)
            <small class="form-text text-muted d-block mt-1">
                {{ $help }}
            </small>
        @endif
    </div>
</div>
