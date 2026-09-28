<x-app-layout>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-2">💱 All Transactions</h1>
        <div class="small-muted">One place to review all income and expenses.</div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-success" href="{{ route('incomes.create') }}">
            <i class="bi bi-plus-lg me-1"></i>Income
        </a>
        <a class="btn btn-danger" href="{{ route('expenses.create') }}">
            <i class="bi bi-plus-lg me-1"></i>Expense
        </a>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card stat-card p-3">
            <div class="small-muted text-uppercase fw-semibold">
                <i class="bi bi-arrow-down-circle text-success me-2"></i>Income
            </div>
            <div class="fs-4 fw-bold text-success mt-2">+ {{ auth()->user()->currency }} {{ number_format($totalIncome, 2) }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card stat-card p-3">
            <div class="small-muted text-uppercase fw-semibold">
                <i class="bi bi-arrow-up-circle text-danger me-2"></i>Expenses
            </div>
            <div class="fs-4 fw-bold text-danger mt-2">- {{ auth()->user()->currency }} {{ number_format($totalExpenses, 2) }}</div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card p-4 mb-4">
    <form class="row g-3">
        <div class="col-12">
            <x-date-range-presets />
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">From Date</label>
            <input class="form-control" type="date" name="from" value="{{ $from }}">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">To Date</label>
            <input class="form-control" type="date" name="to" value="{{ $to }}">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Type</label>
            <select class="form-select" name="type">
                <option value="all" @selected($type==='all')>All types</option>
                <option value="income" @selected($type==='income')>Income only</option>
                <option value="expense" @selected($type==='expense')>Expenses only</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Sort by</label>
            <select class="form-select" name="sort">
                <option value="date_desc" @selected($sort==='date_desc')>Newest first</option>
                <option value="date_asc" @selected($sort==='date_asc')>Oldest first</option>
                <option value="amount_desc" @selected($sort==='amount_desc')>Amount: high to low</option>
                <option value="amount_asc" @selected($sort==='amount_asc')>Amount: low to high</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Search</label>
            <input class="form-control" name="search" value="{{ $search }}" placeholder="Search description...">
        </div>
        <div class="col-md-3 d-flex gap-2 align-items-end">
            <button class="btn btn-primary flex-grow-1">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            @if($from || $to || $search || $type !== 'all' || $sort !== 'date_desc')
                <a class="btn btn-light" href="{{ route('transactions.index') }}" title="Clear all filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 100px;">Type</th>
                    <th style="width: 120px;">Date</th>
                    <th>Description</th>
                    <th>Category/Source</th>
                    <th>Payment Method</th>
                    <th class="text-end" style="width: 140px;">Amount</th>
                    <th class="text-end" style="width: 110px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    <td>
                        <span class="badge text-bg-{{ $row['type']==='income'?'success':'danger' }}">
                            <i class="bi bi-{{ $row['type']==='income'?'arrow-down-circle':'arrow-up-circle' }} me-1"></i>
                            {{ ucfirst($row['type']) }}
                        </span>
                    </td>
                    <td>
                        <span class="small-muted">{{ $row['date']->format('d M Y') }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $row['description'] }}</div>
                    </td>
                    <td>
                        <span class="badge text-bg-light">{{ $row['category'] }}</span>
                    </td>
                    <td class="small-muted">{{ $row['payment'] }}</td>
                    <td class="text-end">
                        <strong class="{{ $row['type']==='income'?'text-success':'text-danger' }}">
                            {{ $row['type']==='income'?'+':'-' }} {{ auth()->user()->currency }} {{ number_format($row['amount'],2) }}
                        </strong>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $row['edit_url'] }}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form class="d-inline" method="POST" action="{{ $row['delete_url'] }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit"
                                    onclick="return confirm('Delete this {{ $row['type'] }}?')" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="bi bi-inbox fs-3"></i>
                        </div>
                        <p class="mb-0">No transactions found.</p>
                        <small class="text-muted">
                            Adjust your filters or <a href="{{route('expenses.create')}}">add your first transaction</a>
                        </small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($rows->hasPages())
        <div class="p-3 border-top">
            {{ $rows->onEachSide(1)->links() }}
        </div>
    @endif
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
        background-color: rgba(59, 130, 246, 0.02);
    }

    .badge {
        font-weight: 500;
        font-size: 0.8rem;
    }
</style>
@endpush

</x-app-layout>
