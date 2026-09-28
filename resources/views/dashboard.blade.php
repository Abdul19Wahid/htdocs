<x-app-layout>

<!-- Budget Alerts Section -->
@if($budgetAlerts && $budgetAlerts->count() > 0)
<div class="mb-4 fade-in">
    @foreach($budgetAlerts as $alert)
        @php
            $style = match($alert->type) {
                'budget_exceeded' => ['danger', 'bi-exclamation-octagon'],
                'budget_alert' => ['warning', 'bi-exclamation-triangle'],
                'savings_trend' => ['info', 'bi-graph-up-arrow'],
                'recurring_due' => ['primary', 'bi-bell'],
                default => ['secondary', 'bi-info-circle'],
            };
        @endphp
        <div class="alert alert-{{ $style[0] }} alert-dismissible fade show" role="alert" data-notification-id="{{ $alert->id }}">
            <div class="d-flex align-items-start gap-2">
                <i class="bi {{ $style[1] }} flex-shrink-0 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>{{ $alert->title }}</strong>
                    <p class="mb-0 small mt-1">{{ $alert->message }}</p>
                    @if($alert->action_url)
                        <a href="{{ $alert->action_url }}" class="btn btn-sm btn-link mt-2 p-0">
                            View <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endif
                </div>
                <button type="button" class="btn-close mark-alert-read" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endforeach
</div>
@push('scripts')
<script>
    // Dismissing an alert here should actually mark it read server-side —
    // otherwise it just reappears on next page load.
    document.querySelectorAll('.mark-alert-read').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.closest('[data-notification-id]').dataset.notificationId;
            fetch(`/notifications/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }).catch(() => {});
        });
    });
</script>
@endpush
@endif

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
    <div>
        <h1 class="page-title mb-2 fade-in">
            Good {{ now()->format('A') === 'AM' ? 'morning' : 'afternoon' }}, 
            {{ explode(' ', trim(auth()->user()->name))[0] ?? auth()->user()->name }} 👋
        </h1>
        <div class="small-muted">Here is your financial overview for {{ now()->format('F Y') }}.</div>
    </div>
    <div class="d-flex gap-2 flex-wrap slide-in-right">
        <a class="btn btn-outline-primary" href="{{ route('reports.index') }}" title="View detailed reports">
            <i class="bi bi-file-earmark-bar-graph me-1"></i><span class="d-none d-sm-inline">Reports</span>
        </a>
        <a class="btn btn-primary" href="{{ route('expenses.create') }}" title="Add a new expense">
            <i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">Expense</span>
        </a>
        <a class="btn btn-outline-success" href="{{ route('incomes.create') }}" title="Add a new income">
            <i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">Income</span>
        </a>
    </div>
</div>

<!-- Key Metrics Cards -->
<div class="row g-4 mb-5">
    @php
        $metrics = [
            ['Total Income', $totalIncome, 'success', 'bi-arrow-down-circle', 'income'],
            ['Total Expenses', $totalExpenses, 'danger', 'bi-arrow-up-circle', 'expenses'],
            ['Current Balance', $totalIncome - $totalExpenses, 'primary', 'bi-wallet2', 'balance'],
            ['This Month', $monthlyIncome - $monthlyExpenses, 'info', 'bi-calendar-event', 'monthly']
        ];
    @endphp
    @foreach($metrics as $stat)
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card p-4 h-100 border-0 shadow-sm stat-{{ $stat[4] }}" role="region" aria-label="{{ $stat[0] }}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="small-muted text-uppercase fw-semibold mb-2">{{ $stat[0] }}</div>
                    <div class="fs-5 fw-bold text-amount" data-amount="{{ $stat[1] }}">
                        {{ auth()->user()->currency }} {{ number_format($stat[1], 2) }}
                    </div>
                </div>
                <div class="stat-icon bg-{{ $stat[2] }}-subtle text-{{ $stat[2] }}">
                    <i class="bi {{ $stat[3] }} fs-5"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top border-opacity-25">
                <small class="text-muted">
                    @switch($stat[4])
                        @case('income')
                            ↑ Income streams
                            @break
                        @case('expenses')
                            ↓ Total spent
                            @break
                        @case('balance')
                            {{ $stat[1] >= 0 ? '✓ Positive' : '✗ Deficit' }}
                            @break
                        @case('monthly')
                            {{ now()->format('M Y') }}
                            @break
                    @endswitch
                </small>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Charts Row -->
<div class="row g-4 mb-5">
    <!-- Cash Flow Chart -->
    <div class="col-xl-8">
        <div class="card p-4 h-100">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">💰 Cash Flow</h5>
                <div class="small-muted">Income and expenses over the last 6 months</div>
            </div>
            <div style="max-height: 300px;">
                <canvas id="cashFlow" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Category Breakdown Chart -->
    <div class="col-xl-4">
        <div class="card p-4 h-100">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">📊 Spending by Category</h5>
                <div class="small-muted">This month's breakdown</div>
            </div>
            @if($categoryBreakdown->count() > 0)
            <div style="max-height: 300px; position: relative;">
                <canvas id="categoryChart" height="100"></canvas>
            </div>
            @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>
                No spending recorded this month.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Budget & Goals Section -->
<div class="row g-4 mb-5">
    <!-- Budget Progress -->
    <div class="col-xl-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">📋 Budget Status</h5>
                    <div class="small-muted">Active budgets</div>
                </div>
                <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-light">Manage</a>
            </div>

            @if($budgetTotal > 0)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold">Overall Spending</span>
                    <span class="badge bg-{{ $budgetPercent > 75 ? 'danger' : ($budgetPercent > 50 ? 'warning' : 'success') }}">
                        {{ $budgetPercent }}%
                    </span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-{{ $budgetPercent > 75 ? 'danger' : ($budgetPercent > 50 ? 'warning' : 'success') }}" 
                         style="width: {{ min($budgetPercent, 100) }}%"></div>
                </div>
                <div class="d-flex justify-content-between mt-2 small-muted">
                    <span>{{ auth()->user()->currency }} {{ number_format($budgetSpent, 2) }} spent</span>
                    <span>{{ auth()->user()->currency }} {{ number_format($budgetTotal - $budgetSpent, 2) }} remaining</span>
                </div>
            </div>

            <hr class="my-3">

            <div class="d-flex justify-content-between">
                <div>
                    <div class="small-muted">Total Budget</div>
                    <div class="fs-5 fw-bold">{{ auth()->user()->currency }} {{ number_format($budgetTotal, 2) }}</div>
                </div>
                <div>
                    <div class="small-muted">Today's Spending</div>
                    <div class="fs-5 fw-bold text-danger">{{ auth()->user()->currency }} {{ number_format($todayExpenses, 2) }}</div>
                </div>
                <div>
                    <div class="small-muted">Monthly Avg</div>
                    <div class="fs-5 fw-bold">{{ auth()->user()->currency }} {{ number_format($monthlyExpenses, 2) }}</div>
                </div>
            </div>
            @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>
                No active budgets. <a href="{{ route('budgets.create') }}">Create one</a>
            </div>
            @endif
        </div>
    </div>

    <!-- Savings Goals -->
    <div class="col-xl-4">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">🎯 Savings Goals</h5>
                <a href="{{ route('savings-goals.index') }}" class="small">View all</a>
            </div>

            @forelse($goals as $goal)
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold small">{{ $goal->name }}</span>
                    <span class="small-muted">{{ $goal->progress_percentage }}%</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ $goal->progress_percentage }}%"></div>
                </div>
                <div class="small-muted mt-1">
                    {{ auth()->user()->currency }} {{ number_format($goal->current_amount, 2) }} / 
                    {{ number_format($goal->target_amount, 2) }}
                </div>
            </div>
            @empty
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>
                No active savings goals. <a href="{{ route('savings-goals.create') }}">Create one</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Activity & Top Categories -->
<div class="row g-4">
    <!-- Recent Expenses -->
    <div class="col-xl-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">💸 Recent Expenses</h5>
                    <div class="small-muted">Your latest spending activity</div>
                </div>
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light">View all</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="small-muted">Date</th>
                            <th class="small-muted">Description</th>
                            <th class="small-muted">Category</th>
                            <th class="text-end small-muted">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentExpenses as $expense)
                        <tr>
                            <td class="small-muted">{{ $expense->date->format('d M') }}</td>
                            <td class="fw-semibold">{{ $expense->description ?: 'Expense' }}</td>
                            <td>
                                <span class="badge rounded-pill text-bg-light">
                                    {{ $expense->category?->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="text-end text-danger fw-bold">
                                -{{ number_format($expense->amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No expenses yet. <a href="{{ route('expenses.create') }}">Add your first expense.</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Spending Categories -->
    <div class="col-xl-4">
        <div class="card p-4">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">📈 Top Categories</h5>
                <div class="small-muted">Highest spending this month</div>
            </div>

            @forelse($categoryBreakdown->take(5) as $category)
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small fw-semibold">{{ $category->category?->name ?? 'Uncategorized' }}</span>
                    <span class="small-muted">{{ auth()->user()->currency }} {{ number_format($category->total, 2) }}</span>
                </div>
                @php
                    $maxAmount = $categoryBreakdown->first()->total ?? 1;
                    $percentage = ($category->total / $maxAmount) * 100;
                @endphp
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
            @empty
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>
                No spending recorded this month.
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Stat Cards Enhancement */
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(37, 99, 235, 0.1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .stat-card:hover::before {
        left: 100%;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 35px rgba(37, 99, 235, 0.15);
        border-color: rgba(37, 99, 235, 0.2);
    }

    .stat-card .text-amount {
        font-size: 1.75rem;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-icon {
        min-width: 56px;
        min-height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1);
    }

    /* Chart Styling */
    .chart-container {
        position: relative;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.02), rgba(16, 185, 129, 0.02));
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    /* Progress Bar Enhancement */
    .progress {
        height: 8px;
        border-radius: 20px;
        background-color: rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .progress-bar {
        background: linear-gradient(90deg, #2563eb, #0ea5e9);
        transition: width 0.6s ease;
        border-radius: 20px;
        position: relative;
    }

    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* Table Enhancement */
    .table th {
        background-color: rgba(37, 99, 235, 0.05);
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        padding: 12px 0.75rem;
    }

    .table tbody tr {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(37, 99, 235, 0.04);
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    /* Badge Enhancement */
    .badge {
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .badge:hover {
        transform: scale(1.05);
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .stat-card {
            background: #1e293b;
            border-color: #334155;
        }

        .stat-card .text-amount {
            background: linear-gradient(135deg, #60a5fa, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .table th {
            background-color: rgba(37, 99, 235, 0.1);
            color: #cbd5e1;
        }

        .chart-container {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(16, 185, 129, 0.1));
        }
    }

    html.dark-mode .stat-card {
        background: #1e293b;
        border-color: #334155;
    }

    html.dark-mode .table th {
        background-color: rgba(37, 99, 235, 0.1);
        color: #cbd5e1;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .stat-card {
            margin-bottom: 1rem;
        }

        .stat-card .text-amount {
            font-size: 1.5rem;
        }

        .stat-icon {
            min-width: 48px;
            min-height: 48px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart.js doesn't know about our dark-mode class and defaults to
    // dark-gray text, which disappears against dark card backgrounds.
    const sfIsDark = document.documentElement.classList.contains('dark-mode');
    const sfChartText = sfIsDark ? '#cbd5e1' : '#475569';
    const sfChartGrid = sfIsDark ? 'rgba(148, 163, 184, 0.15)' : 'rgba(0, 0, 0, 0.05)';
    const sfCardBg = sfIsDark ? '#1e293b' : '#fff';

    // Cash Flow Chart
    new Chart(document.getElementById('cashFlow'), {
        type: 'line',
        data: {
            labels: @json($chart->pluck('label')),
            datasets: [
                {
                    label: 'Income',
                    data: @json($chart->pluck('income')),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Expenses',
                    data: @json($chart->pluck('expense')),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12, weight: '500' },
                        color: sfChartText
                    }
                },
                filler: {
                    propagate: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { size: 11 },
                        color: sfChartText
                    },
                    grid: {
                        color: sfChartGrid
                    }
                },
                x: {
                    ticks: {
                        font: { size: 11 },
                        color: sfChartText
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Category Chart
    @if(count($categoryBreakdown) > 0)
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($categoryBreakdown->pluck('category.name')->map(fn($n) => $n ?? 'Uncategorized')),
            datasets: [{
                data: @json($categoryBreakdown->pluck('total')),
                backgroundColor: [
                    '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
                    '#ec4899', '#06b6d4', '#6366f1', '#f97316', '#14b8a6'
                ],
                borderColor: sfCardBg,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 12,
                        font: { size: 11, weight: '500' },
                        color: sfChartText
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush

</x-app-layout>
