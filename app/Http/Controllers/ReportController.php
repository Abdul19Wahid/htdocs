<?php

namespace App\Http\Controllers;

use App\Services\FinanceCalculator;
use App\Services\FinancialInsightsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private FinanceCalculator $calc, private FinancialInsightsService $insights) {}

    private function queryData(Request $r): array
    {
        $u = $r->user();
        $from = $r->input('from', now()->startOfMonth()->toDateString());
        $to = $r->input('to', now()->endOfMonth()->toDateString());

        $expenses = $u->expenses()
            ->with(['category', 'paymentMethod'])
            ->whereBetween('date', [$from, $to])
            ->latest('date')
            ->latest('id')
            ->get();

        $income = $u->incomes()
            ->with('incomeSource')
            ->whereBetween('date', [$from, $to])
            ->latest('date')
            ->latest('id')
            ->get();

        return [$from, $to, $income, $expenses];
    }

    public function index(Request $r)
    {
        [$from, $to, $income, $expenses] = $this->queryData($r);

        $u = $r->user();
        $incomeTotal = $this->calc->incomeTotal($u, $from, $to);
        $expenseTotal = $this->calc->expenseTotal($u, $from, $to);
        $netTotal = $this->calc->balance($u, $from, $to);
        $savingsRate = $this->calc->savingsRate($u, $from, $to);

        // Expense trend: compare this period's spending to the immediately
        // preceding period of the same length, so "trend" means something
        // even when the user picks an arbitrary custom date range.
        $periodDays = Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1;
        $prevFrom = Carbon::parse($from)->subDays($periodDays)->toDateString();
        $prevTo = Carbon::parse($from)->subDay()->toDateString();
        $prevExpenseTotal = $this->calc->expenseTotal($u, $prevFrom, $prevTo);
        $expenseTrendPercent = $prevExpenseTotal > 0
            ? round((($expenseTotal - $prevExpenseTotal) / $prevExpenseTotal) * 100, 1)
            : null; // null = no prior-period data to compare against

        $byCategory = $expenses
            ->groupBy(fn ($e) => $e->category?->name ?? 'Uncategorized')
            ->map(fn ($rows) => (float) $rows->sum('amount'))
            ->sortDesc();

        $byPaymentMethod = $expenses
            ->groupBy(fn ($e) => $e->paymentMethod?->name ?? 'Unspecified')
            ->map(fn ($rows) => (float) $rows->sum('amount'))
            ->sortDesc();

        $monthly = collect();
        $cursor = Carbon::parse($from)->startOfMonth();
        $lastMonth = Carbon::parse($to)->startOfMonth();

        while ($cursor->lte($lastMonth)) {
            $key = $cursor->format('Y-m');
            $monthly->put($key, [
                'label' => $cursor->format('M Y'),
                'income' => (float) $income->filter(fn ($item) => $item->date->format('Y-m') === $key)->sum('amount'),
                'expenses' => (float) $expenses->filter(fn ($item) => $item->date->format('Y-m') === $key)->sum('amount'),
            ]);
            $cursor->addMonth();
        }

        $transactions = $income->map(fn ($item) => [
            'type' => 'Income',
            'date' => $item->date,
            'description' => $item->description ?: 'Income',
            'category' => $item->incomeSource?->name ?? 'Unspecified',
            'amount' => (float) $item->amount,
        ])->concat($expenses->map(fn ($item) => [
            'type' => 'Expense',
            'date' => $item->date,
            'description' => $item->description ?: 'Expense',
            'category' => $item->category?->name ?? 'Uncategorized',
            'amount' => (float) $item->amount,
        ]))->sortByDesc('date')->values();

        $highestExpense = $expenses->sortByDesc('amount')->first();
        $highestIncome = $income->sortByDesc('amount')->first();
        $topCategory = $this->calc->topSpendingCategory($u, $from, $to);

        // Independent of whatever date range is selected above — always
        // the trailing 6 calendar months from today, so there's always
        // something to compare against even if the filter is a single day
        // or a single month with nothing to contrast it with.
        $monthlyComparison = $this->calc->monthlySeries($u, 6)->values();

        $insightCards = $this->insights->generate($u, $from, $to, $expenses, $income);

        return view('reports.index', compact(
            'from',
            'to',
            'income',
            'expenses',
            'incomeTotal',
            'expenseTotal',
            'netTotal',
            'savingsRate',
            'expenseTrendPercent',
            'byCategory',
            'byPaymentMethod',
            'monthly',
            'monthlyComparison',
            'transactions',
            'highestExpense',
            'highestIncome',
            'topCategory',
            'insightCards'
        ));
    }

    public function csv(Request $r): StreamedResponse
    {
        [$from, $to, $income, $expenses] = $this->queryData($r);

        return response()->streamDownload(function () use ($income, $expenses) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Type', 'Date', 'Description', 'Category/Source', 'Payment Method', 'Amount']);

            foreach ($income as $i) {
                fputcsv($out, [
                    'Income',
                    $i->date->format('Y-m-d'),
                    $i->description ?: 'Income',
                    $i->incomeSource?->name ?? '',
                    $i->paymentMethod?->name ?? '',
                    $i->amount,
                ]);
            }

            foreach ($expenses as $e) {
                fputcsv($out, [
                    'Expense',
                    $e->date->format('Y-m-d'),
                    $e->description ?: 'Expense',
                    $e->category?->name ?? '',
                    $e->paymentMethod?->name ?? '',
                    $e->amount,
                ]);
            }

            fclose($out);
        }, 'smart-finance-report-' . $from . '-to-' . $to . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
