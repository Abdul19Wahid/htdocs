<x-app-layout>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="page-title mb-2">🎯 Savings Goals</h1>
        <div class="small-muted">Turn your plans into measurable progress.</div>
    </div>
    <a class="btn btn-success" href="{{route('savings-goals.create')}}">
        <i class="bi bi-plus-lg me-1"></i>New Goal
    </a>
</div>

<div class="row g-4">
    @forelse($items as $goal)
    <div class="col-md-6 col-lg-4">
        <div class="card p-4 h-100 stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1">{{$goal->name}}</h5>
                    <div class="small-muted">
                        <i class="bi bi-{{ $goal->status === 'completed' ? 'check-circle' : 'hourglass-end' }} me-1"></i>
                        {{ ucfirst($goal->status) }}
                    </div>
                </div>
                <span class="badge {{ $goal->status === 'completed' ? 'text-bg-success' : 'text-bg-info' }}">
                    {{$goal->progress_percentage}}%
                </span>
            </div>

            <div class="mb-3">
                <div class="fs-4 fw-bold">
                    {{auth()->user()->currency}} {{number_format($goal->current_amount,2)}}
                </div>
                <div class="small-muted">
                    of {{auth()->user()->currency}} {{number_format($goal->target_amount,2)}} target
                </div>
            </div>

            <div class="progress mb-3" style="height: 10px;">
                <div class="progress-bar {{ $goal->status === 'completed' ? 'bg-success' : 'bg-info' }}" 
                     style="width:{{ min($goal->progress_percentage, 100) }}%"></div>
            </div>

            <div class="d-flex justify-content-between small-muted mb-3">
                <span>
                    <i class="bi bi-check-lg"></i> {{$goal->progress_percentage}}% Complete
                </span>
                <span>
                    <i class="bi bi-arrow-right"></i> {{auth()->user()->currency}} {{number_format($goal->remaining_amount,2)}} left
                </span>
            </div>

            @if($goal->status === 'completed')
            <div class="alert alert-success py-2 px-2 mb-3 small">
                <i class="bi bi-check-circle me-1"></i>
                Congratulations! Goal completed!
            </div>
            @endif

            <div class="d-flex gap-2 mt-auto">
                <button type="button" class="btn btn-sm btn-success flex-grow-1" data-bs-toggle="modal" data-bs-target="#contribute-{{ $goal->id }}">
                    <i class="bi bi-cash-coin me-1"></i>Add Money
                </button>
                <a class="btn btn-sm btn-outline-primary" href="{{route('savings-goals.edit',$goal)}}" title="Edit">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{route('savings-goals.destroy',$goal)}}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this goal?')" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Money modal -->
    <div class="modal fade" id="contribute-{{ $goal->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('savings-goals.contribute', $goal) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Update "{{ $goal->name }}"</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ auth()->user()->currency }}</span>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required autofocus>
                        </div>
                        <div class="form-text">
                            Enter a positive number to add money, or a negative number (e.g. -50) to withdraw/correct.
                        </div>
                        <div class="small-muted mt-2">
                            Currently {{ auth()->user()->currency }} {{ number_format($goal->current_amount, 2) }}
                            of {{ auth()->user()->currency }} {{ number_format($goal->target_amount, 2) }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
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
            <h5 class="text-muted mb-2">No savings goals yet</h5>
            <p class="text-muted mb-4">Create a goal to start tracking your progress towards your dreams.</p>
            <a class="btn btn-success" href="{{route('savings-goals.create')}}">
                <i class="bi bi-plus-lg me-1"></i>Create Your First Goal
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
