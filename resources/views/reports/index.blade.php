<x-app-layout>
    @php $currency = auth()->user()->currency ?? 'GHS'; @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Reports & Analytics</h1>
            <div class="small-muted">Understand where your money comes from and where it goes.</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('reports.csv', request()->query()) }}">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
            <button type="button" class="btn btn-outline-danger" onclick="window.print()">
                <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
            </button>
        </div>
    </div>

    <div class="card p-3 mb-4">
        <form class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small-muted">From</label>
                <input class="form-control" type="date" name="from" value="{{ $from }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small-muted">To</label>
                <input class="form-control" type="date" name="to" value="{{ $to }}" required>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1"><i class="bi bi-bar-chart-line me-1"></i>Generate Report</button>
                <a class="btn btn-light border" href="{{ route('reports.index') }}">Reset</a>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card p-4 h-100">
                <div class="small-muted">Total Income</div>
                <div class="fs-3 fw-bold text-success mt-1">{{ $currency }} {{ number_format($incomeTotal, 2) }}</div>
                <div class="small-muted mt-2">Money received in this period</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card p-4 h-100">
                <div class="small-muted">Total Expenses</div>
                <div class="fs-3 fw-bold text-danger mt-1">{{ $currency }} {{ number_format($expenseTotal, 2) }}</div>
                <div class="small-muted mt-2">Money spent in this period</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card p-4 h-100">
                <div class="small-muted">Net Cash Flow</div>
                <div class="fs-3 fw-bold mt-1 {{ $netTotal >= 0 ? 'text-success' : 'text-danger' }}">{{ $currency }} {{ number_format($netTotal, 2) }}</div>
                <div class="small-muted mt-2">Income minus expenses</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card p-4 h-100">
                <div class="small-muted">Savings Rate</div>
                <div class="fs-3 fw-bold mt-1">{{ number_format($savingsRate, 1) }}%</div>
                <div class="small-muted mt-2">Net cash flow as % of income</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <div class="small-muted">Expense Trend</div>
                @if($expenseTrendPercent === null)
                    <div class="fs-4 fw-bold mt-1 text-muted">No prior period to compare</div>
                @else
                    <div class="fs-4 fw-bold mt-1 {{ $expenseTrendPercent > 0 ? 'text-danger' : 'text-success' }}">
                        <i class="bi bi-{{ $expenseTrendPercent > 0 ? 'arrow-up-right' : 'arrow-down-right' }} me-1"></i>
                        {{ number_format(abs($expenseTrendPercent), 1) }}%
                        {{ $expenseTrendPercent > 0 ? 'more' : 'less' }} than the previous period
                    </div>
                @endif
                <div class="small-muted mt-2">Compared to an equal-length period right before this one</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <div class="small-muted">Highest Spending Category</div>
                @if($topCategory)
                    <div class="fs-4 fw-bold mt-1">{{ $topCategory['name'] }}</div>
                    <div class="small-muted mt-2">{{ $currency }} {{ number_format($topCategory['total'], 2) }} in this period</div>
                @else
                    <div class="fs-4 fw-bold mt-1 text-muted">No expenses yet</div>
                @endif
            </div>
        </div>
    </div>

    @if($insightCards->isNotEmpty())
    <div class="card p-4 mb-4">
        <h5 class="fw-bold mb-1"><i class="bi bi-lightbulb text-warning me-2"></i>Insights</h5>
        <div class="small-muted mb-3">What stands out about this period, in plain terms.</div>
        <div class="row g-3">
            @foreach($insightCards as $card)
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-2 p-3 rounded-3 border">
                        <i class="bi {{ $card['icon'] }} text-{{ $card['tone'] }} fs-5 flex-shrink-0"></i>
                        <div class="small">{{ $card['text'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Income vs Expenses</h5>
                        <div class="small-muted">Monthly comparison for the selected period.</div>
                    </div>
                </div>
                @if($monthly->isEmpty())
                    <div class="text-muted py-5 text-center">No data for this period.</div>
                @else
                    @php $chartMax = max(1, $monthly->flatMap(fn($m) => [$m['income'], $m['expenses']])->max()); @endphp
                    <div class="report-chart">
                        @foreach($monthly as $month)
                            <div class="chart-month">
                                <div class="chart-bars">
                                    <div class="chart-bar income" style="height: {{ max(4, ($month['income'] / $chartMax) * 180) }}px" title="Income: {{ $currency }} {{ number_format($month['income'], 2) }}"></div>
                                    <div class="chart-bar expense" style="height: {{ max(4, ($month['expenses'] / $chartMax) * 180) }}px" title="Expenses: {{ $currency }} {{ number_format($month['expenses'], 2) }}"></div>
                                </div>
                                <div class="small-muted text-center mt-2">{{ $month['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex gap-4 small-muted mt-3">
                        <span><i class="bi bi-square-fill text-success me-1"></i>Income</span>
                        <span><i class="bi bi-square-fill text-danger me-1"></i>Expenses</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-1">Spending by Category</h5>
                <div class="small-muted mb-3">Where your expenses went.</div>
                @forelse($byCategory as $name => $amount)
                    @php $percent = $expenseTotal > 0 ? ($amount / $expenseTotal) * 100 : 0; @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $name }}</span>
                            <strong>{{ $currency }} {{ number_format($amount, 2) }}</strong>
                        </div>
                        <div class="progress bg-light">
                            <div class="progress-bar" style="width: {{ min(100, $percent) }}%; background-color: {{ ['#3b82f6','#ef4444','#10b981','#f59e0b','#8b5cf6','#ec4899','#06b6d4','#6366f1','#f97316','#14b8a6'][$loop->index % 10] }}"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted py-4">No expenses in this period.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Trailing 6-month comparison — always the last 6 calendar months
         from today, independent of the from/to filter above, so there's
         always a real month-over-month comparison even when the filter
         is a single day or a single month. -->
    <div class="card p-4 mb-4">
        <h5 class="fw-bold mb-1">Monthly Comparison</h5>
        <div class="small-muted mb-3">Last 6 months, income vs expenses, regardless of the filter above.</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-end">Income</th>
                        <th class="text-end">Expenses</th>
                        <th class="text-end">Net</th>
                        <th class="text-end">vs Previous Month</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyComparison as $i => $m)
                        @php
                            $prevExpenses = $i > 0 ? $monthlyComparison[$i - 1]['expenses'] : null;
                            $change = $prevExpenses > 0 ? round((($m['expenses'] - $prevExpenses) / $prevExpenses) * 100, 1) : null;
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $m['label'] }}</td>
                            <td class="text-end text-success">{{ $currency }} {{ number_format($m['income'], 2) }}</td>
                            <td class="text-end text-danger">{{ $currency }} {{ number_format($m['expenses'], 2) }}</td>
                            <td class="text-end {{ $m['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $currency }} {{ number_format($m['net'], 2) }}
                            </td>
                            <td class="text-end">
                                @if($change === null)
                                    <span class="text-muted small">—</span>
                                @else
                                    <span class="small {{ $change > 0 ? 'text-danger' : 'text-success' }}">
                                        <i class="bi bi-{{ $change > 0 ? 'arrow-up-right' : 'arrow-down-right' }}"></i>
                                        {{ number_format(abs($change), 1) }}%
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3">Payment Methods</h5>
                @forelse($byPaymentMethod as $name => $amount)
                    @php $percent = $expenseTotal > 0 ? ($amount / $expenseTotal) * 100 : 0; @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1"><span>{{ $name }}</span><strong>{{ $currency }} {{ number_format($amount, 2) }}</strong></div>
                            <div class="progress bg-light"><div class="progress-bar" style="width: {{ min(100, $percent) }}%"></div></div>
                        </div>
                        <span class="small-muted">{{ number_format($percent, 1) }}%</span>
                    </div>
                @empty
                    <div class="text-muted">No payment-method data for this period.</div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3">Highlights</h5>
                <div class="d-flex justify-content-between border-bottom py-3">
                    <span class="small-muted">Highest expense</span>
                    <strong>{{ $highestExpense ? $currency . ' ' . number_format($highestExpense->amount, 2) : '—' }}</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom py-3">
                    <span class="small-muted">Highest income</span>
                    <strong>{{ $highestIncome ? $currency . ' ' . number_format($highestIncome->amount, 2) : '—' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-3">
                    <span class="small-muted">Transactions</span>
                    <strong>{{ $transactions->count() }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">Financial Statement</h5>
                <div class="small-muted">All transactions in the selected date range.</div>
            </div>
            <span class="badge text-bg-light">{{ $transactions->count() }} transactions</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr><th>Type</th><th>Date</th><th>Description</th><th>Category / Source</th><th class="text-end">Amount</th></tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td><span class="badge {{ $transaction['type'] === 'Income' ? 'text-bg-success' : 'text-bg-danger' }}">{{ $transaction['type'] }}</span></td>
                            <td>{{ $transaction['date']->format('d M Y') }}</td>
                            <td>{{ $transaction['description'] }}</td>
                            <td>{{ $transaction['category'] }}</td>
                            <td class="text-end fw-semibold {{ $transaction['type'] === 'Income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction['type'] === 'Income' ? '+' : '-' }}{{ $currency }} {{ number_format($transaction['amount'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">No transactions found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('styles')
        <style>
            .report-chart { display:flex; align-items:flex-end; gap:1rem; min-height:220px; overflow-x:auto; padding:10px 4px 0; }
            .chart-month { min-width:70px; flex:1; }
            .chart-bars { height:190px; display:flex; align-items:flex-end; justify-content:center; gap:5px; border-bottom:1px solid #e2e8f0; }
            .chart-bar { width:16px; min-height:4px; border-radius:6px 6px 0 0; transition:opacity .2s; }
            .chart-bar:hover { opacity:.75; }
            .chart-bar.income { background:#198754; }
            .chart-bar.expense { background:#dc3545; }

            /* Clean, chrome-free layout when saved as PDF via the browser's print dialog */
            @media print {
                .sidebar, .topbar, .fab-main, .fab-item, nav, form, button,
                a.btn, .offcanvas { display: none !important; }
                .main-content, .page-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
                .card { break-inside: avoid; border: 1px solid #ddd !important; box-shadow: none !important; }
                body { background: #fff !important; }
            }
        </style>
    @endpush
</x-app-layout>
