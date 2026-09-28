<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Single source of truth for every money calculation in the app.
 *
 * Why this exists: totals were previously computed ad-hoc inside
 * DashboardController, ReportController, etc. Each copy risked drifting
 * out of sync (different date-range logic, different rounding, recurring
 * transactions silently excluded). Every screen should call this class
 * instead of writing its own sum() query.
 *
 * Money handling rules followed throughout:
 *  - All amounts are pulled straight from the DB as decimal(12,2) and
 *    cast to float only at the very end, after summation, never before.
 *  - Every public method returns numbers rounded to 2 dp via round(),
 *    so callers never have to remember to format.
 *  - A null/empty date range never throws — it just means "all time".
 */
class FinanceCalculator
{
    /**
     * Total income for a user, optionally within a date range.
     */
    public function incomeTotal(User $user, ?string $from = null, ?string $to = null): float
    {
        $q = $user->incomes();
        $this->applyDateRange($q, $from, $to);
        return $this->round($q->sum('amount'));
    }

    /**
     * Total expenses for a user, optionally within a date range.
     */
    public function expenseTotal(User $user, ?string $from = null, ?string $to = null): float
    {
        $q = $user->expenses();
        $this->applyDateRange($q, $from, $to);
        return $this->round($q->sum('amount'));
    }

    /**
     * Net balance (income - expenses) for a user, optionally within a date range.
     */
    public function balance(User $user, ?string $from = null, ?string $to = null): float
    {
        return $this->round(
            $this->incomeTotal($user, $from, $to) - $this->expenseTotal($user, $from, $to)
        );
    }

    /**
     * Savings rate as a percentage: (income - expenses) / income * 100.
     * Returns 0 when income is 0 rather than dividing by zero.
     */
    public function savingsRate(User $user, ?string $from = null, ?string $to = null): float
    {
        $income = $this->incomeTotal($user, $from, $to);
        if ($income <= 0) {
            return 0.0;
        }
        return $this->round((($income - $this->expenseTotal($user, $from, $to)) / $income) * 100);
    }

    /**
     * Income + expense totals for the current calendar month.
     */
    public function monthlyTotals(User $user, ?Carbon $month = null): array
    {
        $month ??= now();
        $from = $month->copy()->startOfMonth()->toDateString();
        $to = $month->copy()->endOfMonth()->toDateString();

        return [
            'income' => $this->incomeTotal($user, $from, $to),
            'expenses' => $this->expenseTotal($user, $from, $to),
            'net' => $this->balance($user, $from, $to),
        ];
    }

    /**
     * Income + expense totals for the last $months calendar months
     * (oldest first), for trend charts.
     */
    public function monthlySeries(User $user, int $months = 6): Collection
    {
        return collect(range($months - 1, 0))->map(function (int $n) use ($user) {
            $date = now()->subMonths($n);
            $totals = $this->monthlyTotals($user, $date);
            return [
                'label' => $date->format('M Y'),
                'key' => $date->format('Y-m'),
                'income' => $totals['income'],
                'expenses' => $totals['expenses'],
                'net' => $totals['net'],
            ];
        });
    }

    /**
     * Expense totals grouped by category, optionally within a date range.
     * Returns a collection sorted highest-spend first, keyed by category
     * name ("Uncategorized" for null category_id).
     */
    public function categoryBreakdown(User $user, ?string $from = null, ?string $to = null): Collection
    {
        $q = $user->expenses()->with('category:id,name,color');
        $this->applyDateRange($q, $from, $to);

        return $q->get()
            ->groupBy(fn ($e) => $e->category?->name ?? 'Uncategorized')
            ->map(fn ($rows) => $this->round($rows->sum('amount')))
            ->sortDesc();
    }

    /**
     * The single highest-spending category in the range, or null if no
     * expenses exist. Returns ['name' => ..., 'total' => ...].
     */
    public function topSpendingCategory(User $user, ?string $from = null, ?string $to = null): ?array
    {
        $breakdown = $this->categoryBreakdown($user, $from, $to);
        if ($breakdown->isEmpty()) {
            return null;
        }
        return ['name' => $breakdown->keys()->first(), 'total' => $breakdown->first()];
    }

    /**
     * Percentage of a budget's own category+range that has been spent.
     * Mirrors BudgetAlertService's math so both stay consistent.
     */
    public function budgetSpentPercent(User $user, float $budgetAmount, string $from, string $to, ?int $categoryId = null): float
    {
        if ($budgetAmount <= 0) {
            return 0.0;
        }
        $q = $user->expenses()->whereBetween('date', [$from, $to]);
        if ($categoryId) {
            $q->where('category_id', $categoryId);
        }
        $spent = (float) $q->sum('amount');
        return $this->round(min(($spent / $budgetAmount) * 100, 999));
    }

    private function applyDateRange($query, ?string $from, ?string $to): void
    {
        if ($from && $to) {
            $query->whereBetween('date', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('date', '>=', $from);
        } elseif ($to) {
            $query->whereDate('date', '<=', $to);
        }
    }

    private function round(mixed $value): float
    {
        return round((float) $value, 2);
    }
}
