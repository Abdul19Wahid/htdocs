<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="page-title">Expense Details</h1></div><div><a class="btn btn-primary" href="{{ route('expenses.edit', $expense) }}">Edit</a> <a class="btn btn-light" href="{{ route('expenses.index') }}">Back</a></div></div>
    <div class="card p-4" style="max-width:850px"><div class="row g-4">
        <div class="col-md-6"><div class="small-muted">Amount</div><div class="fs-2 fw-bold text-danger">-{{ auth()->user()->currency }} {{ number_format($expense->amount, 2) }}</div></div>
        <div class="col-md-6"><div class="small-muted">Date</div><div class="fw-semibold">{{ $expense->date->format('d M Y') }}</div></div>
        <div class="col-md-6"><div class="small-muted">Category</div><div>{{ $expense->category?->name ?? 'Uncategorized' }}</div></div>
        <div class="col-md-6"><div class="small-muted">Payment method</div><div>{{ $expense->paymentMethod?->name ?? '—' }}</div></div>
        <div class="col-12"><div class="small-muted">Description</div><div>{{ $expense->description ?: 'Expense' }}</div></div>
        <div class="col-12"><div class="small-muted">Notes</div><div>{{ $expense->notes ?: 'No notes.' }}</div></div>
        @if($expense->receipt)<div class="col-12"><a class="btn btn-outline-secondary" target="_blank" href="{{ asset('storage/'.$expense->receipt) }}"><i class="bi bi-paperclip me-1"></i>View receipt</a></div>@endif
    </div></div>
</x-app-layout>
