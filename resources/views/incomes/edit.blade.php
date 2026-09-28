<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="page-title">Edit Income</h1></div><a class="btn btn-light" href="{{ route('incomes.index') }}">Back</a></div>
    <div class="card p-4" style="max-width:900px"><form method="POST" action="{{ route('incomes.update', $income) }}">@csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Income source</label><select class="form-select" name="income_source_id"><option value="">Select source</option>@foreach($sources as $s)<option value="{{ $s->id }}" @selected(old('income_source_id', $income->income_source_id) == $s->id)>{{ $s->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Payment method</label><select class="form-select" name="payment_method_id"><option value="">Select method</option>@foreach($methods as $m)<option value="{{ $m->id }}" @selected(old('payment_method_id', $income->payment_method_id) == $m->id)>{{ $m->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Amount</label><div class="input-group"><span class="input-group-text">{{ auth()->user()->currency }}</span><input class="form-control" type="number" min="0.01" step="0.01" name="amount" value="{{ old('amount', $income->amount) }}" required></div></div>
            <div class="col-md-6"><label class="form-label">Date</label><input class="form-control" type="date" name="date" value="{{ old('date', $income->date?->format('Y-m-d')) }}" max="{{ today()->format('Y-m-d') }}" required></div>
            <div class="col-12"><label class="form-label">Description</label><input class="form-control" name="description" value="{{ old('description', $income->description) }}"></div>
            <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" name="notes" rows="4">{{ old('notes', $income->notes) }}</textarea></div>
        </div><div class="mt-4"><button class="btn btn-primary">Update Income</button></div>
    </form></div>
</x-app-layout>
