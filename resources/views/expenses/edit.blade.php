<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="page-title mb-1">Edit Expense</h1><div class="small-muted">Update this expense record.</div></div><a class="btn btn-light" href="{{ route('expenses.index') }}">Back</a></div>
    <div class="card p-4" style="max-width: 900px">
        <form method="POST" enctype="multipart/form-data" action="{{ route('expenses.update', $expense) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Category</label><select class="form-select" name="category_id"><option value="">Uncategorized</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $expense->category_id) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Payment method</label><select class="form-select" name="payment_method_id"><option value="">Select method</option>@foreach($methods as $m)<option value="{{ $m->id }}" @selected(old('payment_method_id', $expense->payment_method_id) == $m->id)>{{ $m->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Amount</label><div class="input-group"><span class="input-group-text">{{ auth()->user()->currency }}</span><input class="form-control" type="number" min="0.01" step="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" required></div></div>
                <div class="col-md-6"><label class="form-label">Date</label><input class="form-control" type="date" name="date" value="{{ old('date', $expense->date?->format('Y-m-d')) }}" max="{{ today()->format('Y-m-d') }}" required></div>
                <div class="col-12"><label class="form-label">Description</label><input class="form-control" name="description" value="{{ old('description', $expense->description) }}"></div>
                <div class="col-12"><label class="form-label">Replace receipt <span class="small-muted">JPG, PNG or PDF up to 5MB</span></label><input class="form-control" type="file" name="receipt" accept="image/jpeg,image/png,application/pdf">@if($expense->receipt)<div class="small-muted mt-1">A receipt is currently attached.</div>@endif</div>
                <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" name="notes" rows="4">{{ old('notes', $expense->notes) }}</textarea></div>
            </div>
            <div class="mt-4"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Expense</button></div>
        </form>
    </div>
</x-app-layout>
