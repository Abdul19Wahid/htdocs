@props(['fromName' => 'from', 'toName' => 'to'])

{{--
    Quick date-range presets. Sits above/beside the From/To inputs on a GET
    filter form. Clicking a preset fills the from/to date inputs (matched by
    name) and submits the form immediately — no JS date-math library needed,
    each preset's actual dates are computed server-side in PHP below.
--}}
@php
    $today = today();
    $presets = [
        'Today' => [$today->toDateString(), $today->toDateString()],
        'This Week' => [$today->copy()->startOfWeek()->toDateString(), $today->copy()->endOfWeek()->toDateString()],
        'This Month' => [$today->copy()->startOfMonth()->toDateString(), $today->copy()->endOfMonth()->toDateString()],
        'Last Month' => [$today->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(), $today->copy()->subMonthNoOverflow()->endOfMonth()->toDateString()],
        'This Year' => [$today->copy()->startOfYear()->toDateString(), $today->copy()->endOfYear()->toDateString()],
    ];
@endphp

<div class="date-preset-group d-flex flex-wrap gap-2 mb-1">
    @foreach($presets as $label => [$from, $to])
        <button type="button"
                class="btn btn-sm date-preset-btn {{ request($fromName) === $from && request($toName) === $to ? 'btn-primary' : 'btn-outline-secondary' }}"
                data-from="{{ $from }}"
                data-to="{{ $to }}"
                data-from-name="{{ $fromName }}"
                data-to-name="{{ $toName }}">
            {{ $label }}
        </button>
    @endforeach
</div>

@once
@push('scripts')
<script>
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.date-preset-btn');
        if (!btn) return;
        const form = btn.closest('form');
        if (!form) return;
        const fromInput = form.querySelector(`[name="${btn.dataset.fromName}"]`);
        const toInput = form.querySelector(`[name="${btn.dataset.toName}"]`);
        if (fromInput) fromInput.value = btn.dataset.from;
        if (toInput) toInput.value = btn.dataset.to;
        form.submit();
    });
</script>
@endpush
@endonce
