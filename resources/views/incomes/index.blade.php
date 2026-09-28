<x-app-layout>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-2">💰 Income</h1>
        <div class="small-muted">Track all money coming in from various sources.</div>
    </div>
    <a class="btn btn-success" href="{{route('incomes.create')}}">
        <i class="bi bi-plus-lg me-1"></i>Add Income
    </a>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card stat-card p-3">
            <div class="small-muted text-uppercase fw-semibold">Total Income (All Time)</div>
            <div class="fs-4 fw-bold text-success mt-2">+ {{ auth()->user()->currency }} {{ number_format($items->sum('amount'), 2) }}</div>
        </div>
    </div>
    @if(request('from') && request('to'))
    <div class="col-md-6">
        <div class="card stat-card p-3">
            <div class="small-muted text-uppercase fw-semibold">Income in View</div>
            <div class="fs-4 fw-bold text-success mt-2">+ {{ auth()->user()->currency }} {{ number_format($items->where('date', '>=', \Carbon\Carbon::parse(request('from')))->where('date', '<=', \Carbon\Carbon::parse(request('to')))->sum('amount'), 2) }}</div>
        </div>
    </div>
    @endif
</div>

<!-- Filters Card -->
<div class="card p-4 mb-4">
    <form method="GET" class="row g-3">
        <div class="col-12">
            <x-date-range-presets />
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">From Date</label>
            <input class="form-control" type="date" name="from" value="{{request('from')}}">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">To Date</label>
            <input class="form-control" type="date" name="to" value="{{request('to')}}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Income Source</label>
            <select class="form-select" name="source">
                <option value="">All sources</option>
                @foreach($sources as $s)
                <option value="{{$s->id}}" @selected(request('source')==$s->id)>{{$s->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Search</label>
            <input class="form-control" name="search" value="{{request('search')}}" placeholder="Search description...">
        </div>
        <div class="col-md-2 d-flex gap-2 align-items-end">
            <button class="btn btn-primary flex-grow-1">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a class="btn btn-light" href="{{route('incomes.index')}}" title="Clear filters">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<!-- Income Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Source</th>
                    <th>Description</th>
                    <th>Payment Method</th>
                    <th class="text-end">Amount</th>
                    <th class="text-center" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $income)
                <tr>
                    <td>
                        <span class="small-muted">{{$income->date->format('d M Y')}}</span>
                    </td>
                    <td>
                        <span class="badge text-bg-light">{{$income->incomeSource?->name??'No Source'}}</span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{$income->description?:'Income'}}</div>
                    </td>
                    <td class="small-muted">{{$income->paymentMethod?->name??'—'}}</td>
                    <td class="text-end">
                        <strong class="text-success">+ {{ number_format($income->amount,2) }}</strong>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-outline-primary" href="{{route('incomes.edit',$income)}}" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{route('incomes.destroy',$income)}}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="bi bi-inbox fs-3"></i>
                        </div>
                        <p class="mb-0">No income entries found.</p>
                        <small class="text-muted">
                            <a href="{{route('incomes.create')}}">Record your first income</a>
                        </small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
    .stat-card {
        transition: all 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .table th {
        background-color: rgba(0, 0, 0, 0.02);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
    }

    .table tbody tr {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(16, 185, 129, 0.02);
    }

    .badge {
        font-weight: 500;
        font-size: 0.8rem;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

</x-app-layout>
