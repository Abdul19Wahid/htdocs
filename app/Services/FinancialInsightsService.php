<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Turns the numbers FinanceCalculator already produces into plain-language
 * observations — "Housing made up 42% of your spending" rather than just a
 * category-breakdown table. Distinct from InsightAlertService/BudgetAlertService:
 * those write persistent Notification rows the user can dismiss; this class
 * is read-only, regenerated fresh on every Reports page view, and never
 * touches the notifications table.
 */
class FinancialInsightsService
{
    public function __construct(private FinanceCalculator $calc) {}

    /**
     * @return Collection<int, array{icon: string, tone: string, text: string}>
     */
    public function generate(User $user, string $from, string $to, Collection $expenses, Collection $income): Collection
    {
        $insights = collect();

        $incomeTotal = $this->calc->incomeTotal($user, $from, $to);
        $expenseTotal = $this->calc->expenseTotal($user, $from, $to);

        // Savings rate
        if ($incomeTotal > 0) {
            $rate = $this->calc->savingsRate($user, $from, $to);
            if ($rate >= 20) {
                $insights->push(['icon' => 'bi-piggy-bank', 'tone' => 'success', 'text' => "You kept {$rate}% of your income this period — a solid savings cushion."]);
            } elseif ($rate >= 0) {
                $insights->push(['icon' => 'bi-graph-up', 'tone' => 'info', 'text' => "You saved {$rate}% of your income this period. Aiming for 20%+ gives you more breathing room."]);
            } else {
                $overspend = number_format(abs($expenseTotal - $incomeTotal), 2);
                $insights->push(['icon' => 'bi-exclamation-triangle', 'tone' => 'danger', 'text' => "You spent {$user->currency} {$overspend} more than you earned this period."]);
            }
        }

        // Category concentration
        $byCategory = $expenses->groupBy(fn ($e) => $e->category?->name ?? 'Uncategorized')
            ->map(fn ($rows) => (float) $rows->sum('amount'))
            ->sortDesc();

        if ($byCategory->isNotEmpty() && $expenseTotal > 0) {
            $topName = $byCategory->keys()->first();
            $topAmount = $byCategory->first();
            $topPercent = round(($topAmount / $expenseTotal) * 100);
            if ($topPercent >= 30) {
                $insights->push(['icon' => 'bi-pie-chart', 'tone' => 'warning', 'text' => "{$topName} made up {$topPercent}% of your spending — your single biggest category this period."]);
            }

            if ($byCategory->count() >= 3) {
                $top3Percent = round(($byCategory->take(3)->sum() / $expenseTotal) * 100);
                $insights->push(['icon' => 'bi-bar-chart', 'tone' => 'info', 'text' => "Your top 3 categories ({$byCategory->keys()->take(3)->implode(', ')}) account for {$top3Percent}% of all spending."]);
            }
        }

        // Biggest single expense
        $biggest = $expenses->sortByDesc('amount')->first();
        if ($biggest && $expenseTotal > 0) {
            $biggestPercent = round(((float) $biggest->amount / $expenseTotal) * 100);
            if ($biggestPercent >= 15) {
                $desc = $biggest->description ?: ($biggest->category?->name ?? 'this expense');
                $insights->push(['icon' => 'bi-receipt', 'tone' => 'secondary', 'text' => "Your single largest expense — {$desc} at {$user->currency} ".number_format((float) $biggest->amount, 2)." — was {$biggestPercent}% of all spending on its own."]);
            }
        }

        // Spending-heavy day of week (only meaningful with enough data)
        if ($expenses->count() >= 6) {
            $byDay = $expenses->groupBy(fn ($e) => $e->date->format('l'))
                ->map(fn ($rows) => (float) $rows->sum('amount'))
                ->sortDesc();
            if ($byDay->isNotEmpty()) {
                $topDay = $byDay->keys()->first();
                $topDayPercent = round(($byDay->first() / $expenseTotal) * 100);
                if ($topDayPercent >= 25) {
                    $insights->push(['icon' => 'bi-calendar-week', 'tone' => 'info', 'text' => "{$topDay}s were your heaviest spending day, at {$topDayPercent}% of the period's total."]);
                }
            }
        }

        // No income recorded at all
        if ($income->isEmpty() && $expenses->isNotEmpty()) {
            $insights->push(['icon' => 'bi-info-circle', 'tone' => 'secondary', 'text' => "No income recorded for this period — your net figure only reflects spending."]);
        }

        return $insights->take(5)->values();
    }
}
