<x-app-layout>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="page-title mb-2">📊 Budgets</h1>
        <div class="small-muted">Set spending limits and track your progress.</div>
    </div>
    <a class="btn btn-primary" href="{{route('budgets.create')}}">
        <i class="bi bi-plus-lg me-1"></i>Create Budget
    </a>
</div>

<div class="row g-4">
    @forelse($items as $budget)
    <div class="col-md-6 col-lg-4">
        <div class="card p-4 h-100 stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1">
                        {{$budget->name}}
                        @if($budget->is_recurring)
                            <i class="bi bi-arrow-repeat text-primary ms-1" title="Repeats monthly"></i>
                        @endif
                    </h5>
                    <div class="small-muted">{{$budget->category?->name??'All categories'}}</div>
                </div>
                <span class="badge {{ $budget->percent >= 100 ? 'text-bg-danger' : ($budget->percent >= $budget->alert_percentage ? 'text-bg-warning' : 'text-bg-success') }}">
                    {{$budget->percent}}%
                </span>
            </div>

            <div class="mb-3">
                <div class="fs-4 fw-bold">
                    {{auth()->user()->currency}} {{number_format($budget->spent,2)}}
                </div>
                <div class="small-muted">
                    of {{auth()->user()->currency}} {{number_format($budget->amount,2)}}
                </div>
            </div>

            <div class="progress mb-3" style="height: 10px;">
                <div class="progress-bar {{ $budget->percent >= 100 ? 'bg-danger' : ($budget->percent >= $budget->alert_percentage ? 'bg-warning' : 'bg-success') }}" 
                     style="width:{{ min($budget->percent, 100) }}%"></div>
            </div>

            <div class="small-muted mb-3">
                <i class="bi bi-calendar3 me-1"></i>
                {{$budget->start_date->format('d M')}} → {{$budget->end_date->format('d M Y')}}
            </div>

            @if($budget->percent >= $budget->alert_percentage)
            <div class="alert alert-{{ $budget->percent >= 100 ? 'danger' : 'warning' }} py-2 px-2 mb-3 small">
                <i class="bi bi-exclamation-circle me-1"></i>
                {{ $budget->percent >= 100 ? 'Budget exceeded!' : 'Approaching budget limit' }}
            </div>
            @endif

            <div class="d-flex gap-2 mt-auto">
                <a class="btn btn-sm btn-outline-primary flex-grow-1" href="{{route('budgets.edit',$budget)}}">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <form method="POST" action="{{route('budgets.destroy',$budget)}}" class="flex-grow-1">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Delete this budget?')">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card p-5 text-center">
            <div class="text-muted mb-3">
                <i class="bi bi-inbox fs-2"></i>
            </div>
            <h5 class="text-muted mb-2">No budgets yet</h5>
            <p class="text-muted mb-4">Create a budget to start controlling your spending.</p>
            <a class="btn btn-primary" href="{{route('budgets.create')}}">
                <i class="bi bi-plus-lg me-1"></i>Create Your First Budget
            </a>
        </div>
    </div>
    @endforelse
</div>

@if($items->count() > 0)
<div class="d-flex justify-content-center mt-4">
    {{$items->links()}}
</div>
@endif

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    }

    .badge {
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
    }

    .alert {
        border-radius: 8px;
        border: none;
        margin-bottom: 0;
    }
</style>
@endpush

</x-app-layout>
