<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Payment Method</h1>
        <a class="btn btn-light" href="{{ route('payment-methods.index') }}">Back</a>
    </div>
    <div class="card p-4" style="max-width:800px">
        <form method="POST" action="{{ route('payment-methods.update', $item) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $item->name) }}" required>
                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Icon</label>
                <select class="form-select" name="icon">
                    @foreach(['bi-cash-stack'=>'Cash','bi-phone'=>'Mobile Money','bi-bank'=>'Bank Transfer','bi-credit-card'=>'Card','bi-wallet2'=>'Wallet','bi-piggy-bank'=>'Savings','bi-globe'=>'Other/Online'] as $icon => $label)
                        <option value="{{ $icon }}" @selected(old('icon', $item->icon) == $icon)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault" @checked(old('is_default', $item->is_default))>
                <label class="form-check-label" for="isDefault">
                    Set as my default payment method (auto-selected when adding expenses/income)
                </label>
            </div>
            <button class="btn btn-primary">Update Payment Method</button>
        </form>
    </div>
</x-app-layout>
