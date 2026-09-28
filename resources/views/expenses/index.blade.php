<x-app-layout>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
    <div>
        <h1 class="page-title mb-2">💸 Expenses</h1>
        <div class="small-muted">Track and understand your spending patterns.</div>
    </div>
    <a class="btn btn-danger" href="{{route('expenses.create')}}" title="Add a new expense">
        <i class="bi bi-plus-lg me-1"></i>Add Expense
    </a>
</div>

<!-- Summary Stats -->
<div class="row g-4 mb-5">
    <div class="col-sm-6 col-lg-3">
        <x-stat-card 
            title="Total Expenses" 
            :value="$items->sum('amount')" 
            variant="danger"
            icon="bi-arrow-up-circle"
            :currency="auth()->user()->currency"
            trend-label="All time spending"
        />
    </div>
    @if(request('from') && request('to'))
        @php
            $filteredSum = $items->where('date', '>=', \Carbon\Carbon::parse(request('from')))
                               ->where('date', '<=', \Carbon\Carbon::parse(request('to')))
                               ->sum('amount');
        @endphp
        <div class="col-sm-6 col-lg-3">
            <x-stat-card 
                title="Filtered Total" 
                :value="$filteredSum" 
                variant="warning"
                icon="bi-funnel"
                :currency="auth()->user()->currency"
                trend-label="In selected range"
            />
        </div>
    @endif
    <div class="col-sm-6 col-lg-3">
        <x-stat-card 
            title="Total Entries" 
            :value="$items->count()" 
            variant="info"
            icon="bi-list-ul"
            :currency="''"
            trend-label="Expense records"
        />
    </div>
    <div class="col-sm-6 col-lg-3">
        <x-stat-card 
            title="Avg. Expense" 
            :value="$items->count() > 0 ? $items->sum('amount') / $items->count() : 0" 
            variant="primary"
            icon="bi-graph-up"
            :currency="auth()->user()->currency"
            trend-label="Per transaction"
        />
    </div>
</div>

<!-- Enhanced Filters -->
<x-card title="🔍 Filter & Search" subtitle="Refine your expense view">
    <form method="GET" class="row g-3">
        <div class="col-12">
            <x-date-range-presets />
        </div>

        <!-- Date Range -->
        <div class="col-md-3">
            <x-form-group label="From Date" help="Start date">
                <input class="form-control" type="date" name="from" value="{{request('from')}}" title="Filter from this date">
            </x-form-group>
        </div>
        
        <div class="col-md-3">
            <x-form-group label="To Date" help="End date">
                <input class="form-control" type="date" name="to" value="{{request('to')}}" title="Filter until this date">
            </x-form-group>
        </div>

        <!-- Category Filter -->
        <div class="col-md-3">
            <x-form-group label="Category" help="Select category">
                <select class="form-select" name="category" title="Filter by category">
                    <option value="">All categories</option>
                    @foreach($categories as $c)
                        <option value="{{$c->id}}" @selected(request('category')==$c->id)>{{$c->name}}</option>
                    @endforeach
                </select>
            </x-form-group>
        </div>

        <!-- Search -->
        <div class="col-md-3">
            <x-form-group label="Search" help="Description/notes">
                <input class="form-control" 
                       name="search" 
                       value="{{request('search')}}" 
                       placeholder="Search by description..." 
                       title="Search expenses"
                       aria-label="Search expenses">
            </x-form-group>
        </div>

        <!-- Action Buttons -->
        <div class="col-12 d-flex gap-2 pt-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel me-1"></i>Apply Filters
            </button>
            @if(request('from') || request('to') || request('category') || request('search'))
                <a class="btn btn-light" href="{{route('expenses.index')}}" title="Clear all filters">
                    <i class="bi bi-x-lg me-1"></i>Clear Filters
                </a>
            @endif
            <button type="button" class="btn btn-outline-secondary ms-auto" onclick="exportTable()" title="Export as CSV">
                <i class="bi bi-download me-1"></i>Export
            </button>
        </div>
    </form>
</x-card>

<!-- Expenses Table -->
<div class="card mt-4 border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0" id="expensesTable">
            <thead class="bg-light">
                <tr>
                    <th class="text-muted fw-semibold">Date</th>
                    <th class="text-muted fw-semibold">Description</th>
                    <th class="text-muted fw-semibold">Category</th>
                    <th class="text-muted fw-semibold">Payment</th>
                    <th class="text-end text-muted fw-semibold">Amount</th>
                    <th class="text-center text-muted fw-semibold" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $e)
                <tr class="expense-row" data-amount="{{ $e->amount }}" data-category="{{ $e->category?->id ?? 'uncategorized' }}">
                    <td>
                        <span class="small-muted fw-medium" title="{{ $e->date->format('l, F j, Y') }}">
                            {{ $e->date->format('d M Y') }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold text-truncate" title="{{ $e->description ?? 'Expense' }}">
                            {{ $e->description ?? 'Expense' }}
                        </div>
                        @if($e->receipt)
                        <div class="small text-muted mt-1">
                            <i class="bi bi-paperclip me-1"></i>
                            <a href="#" class="text-decoration-none">Receipt attached</a>
                        </div>
                        @endif
                        @if($e->notes)
                        <div class="small text-muted mt-1" title="{{ $e->notes }}">
                            <i class="bi bi-chat-left-text me-1"></i>
                            {{ Str::limit($e->notes, 50) }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary fw-semibold">
                            {{ $e->category?->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td>
                        <span class="small-muted fw-medium">
                            {{ $e->paymentMethod?->name ?? '—' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <strong class="text-danger fs-6">
                            -{{ auth()->user()->currency }}{{ number_format($e->amount, 2) }}
                        </strong>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Expense actions">
                            <a class="btn btn-outline-primary" 
                               href="{{route('expenses.edit',$e)}}" 
                               title="Edit expense"
                               aria-label="Edit this expense">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{route('expenses.destroy',$e)}}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger" 
                                        onclick="return confirm('Are you sure you want to delete this expense?')" 
                                        title="Delete expense"
                                        aria-label="Delete this expense">
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
                        <p class="mb-2">No expenses found.</p>
                        <small class="text-muted">
                            @if(request('from') || request('to') || request('category') || request('search'))
                                <a href="{{route('expenses.index')}}" class="text-decoration-none">Clear filters</a> or
                            @endif
                            <a href="{{route('expenses.create')}}" class="text-decoration-none">create your first expense</a>
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
    .expense-row {
        transition: all 0.2s ease;
    }

    .expense-row:hover {
        background-color: rgba(37, 99, 235, 0.04);
        box-shadow: inset 0 0 12px rgba(37, 99, 235, 0.08);
    }

    .table th {
        background-color: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
        padding: 1rem 0.75rem;
    }

    .table tbody tr {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.7rem;
    }

    /* Dark mode */
    @media (prefers-color-scheme: dark) {
        .table th {
            background-color: #1f2937;
        }

        .expense-row:hover {
            background-color: rgba(37, 99, 235, 0.1);
        }
    }

    html.dark-mode .table th {
        background-color: #1f2937;
    }

    html.dark-mode .expense-row:hover {
        background-color: rgba(37, 99, 235, 0.1);
    }

    /* Responsive table */
    @media (max-width: 768px) {
        .table-responsive .table {
            font-size: 0.9rem;
        }

        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function exportTable() {
        const table = document.getElementById('expensesTable');
        let csv = [];
        
        // Headers
        const headers = Array.from(table.querySelectorAll('thead th'))
            .map(th => th.textContent.trim());
        csv.push(headers.join(','));
        
        // Rows
        table.querySelectorAll('tbody tr:not(:has(td[colspan]))').forEach(row => {
            const cells = Array.from(row.querySelectorAll('td'))
                .map(td => '"' + td.textContent.trim().replace(/"/g, '""') + '"');
            csv.push(cells.join(','));
        });
        
        // Download
        const csvContent = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv.join('\n'));
        const link = document.createElement('a');
        link.setAttribute('href', csvContent);
        link.setAttribute('download', 'expenses_' + new Date().toISOString().split('T')[0] + '.csv');
        link.click();
    }
</script>
@endpush
                @forelse($items as $e)
                <tr>
                    <td>
                        <span class="small-muted">{{$e->date->format('d M Y')}}</span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{$e->description?:'Expense'}}</div>
                        @if($e->receipt)
                        <div class="small text-muted">
                            <i class="bi bi-paperclip me-1"></i>Receipt attached
                        </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge text-bg-light">{{$e->category?->name??'Uncategorized'}}</span>
                    </td>
                    <td class="small-muted">{{$e->paymentMethod?->name??'—'}}</td>
                    <td class="text-end">
                        <strong class="text-danger">-{{ number_format($e->amount,2) }}</strong>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-outline-primary" href="{{route('expenses.edit',$e)}}" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{route('expenses.destroy',$e)}}" class="d-inline" style="display:inline;">
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
                        <p class="mb-0">No expenses found.</p>
                        <small class="text-muted">
                            <a href="{{route('expenses.create')}}">Create your first expense</a>
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
        background-color: rgba(59, 130, 246, 0.02);
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
