<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Payment Methods</h1>
            <div class="small-muted">Manage how you pay and receive money.</div>
        </div>
        <a class="btn btn-primary" href="{{ route('payment-methods.create') }}"><i class="bi bi-plus-lg me-1"></i>Add Payment Method</a>
    </div>
    <div class="card p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Name</th><th>Income Records</th><th>Expense Records</th><th></th></tr></thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="fw-semibold">
                                <i class="bi {{ $item->icon ?: 'bi-wallet2' }} me-2 text-muted"></i>{{ $item->name }}
                                @if($item->is_default)
                                    <span class="badge bg-primary-subtle text-primary ms-2">Default</span>
                                @endif
                            </td>
                            <td>{{ $item->incomes_count ?? 0 }}</td>
                            <td>{{ $item->expenses_count ?? 0 }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('payment-methods.edit', $item) }}">Edit</a>
                                <form class="d-inline" method="POST" action="{{ route('payment-methods.destroy', $item) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this payment method?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Nothing here yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</x-app-layout>
