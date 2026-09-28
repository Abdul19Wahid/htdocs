<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\RecurringTransaction;
use App\Models\User;
use Carbon\Carbon;

/**
 * Generates the two alert types BudgetAlertService doesn't cover:
 *   - "your savings changed by X% this month" (a trend alert, not a
 *     threshold alert, so it needs its own comparison logic)
 *   - "your <recurring item> is due tomorrow" (based on next_due_date on
 *     recurring_transactions, independent of budgets entirely)
 *
 * Both write into the same `notifications` table as BudgetAlertService,
 * using distinct `type` values, so the notification bell/list already
 * shows them without any UI changes.
 */
class InsightAlertService
{
    public function __construct(private FinanceCalculator $calc) {}

    /**
     * Compare this month's net savings (income - expenses) to last
     * month's, and raise an alert on a meaningful swing either way.
     * Skipped entirely if last month had no income (nothing to compare
     * against — a 0 -> anything change isn't a meaningful percentage).
     */
    public function checkSavingsTrend(User $user): ?Notification
    {
        $thisMonth = $this->calc->monthlyTotals($user, now());
        $lastMonth = $this->calc->monthlyTotals($user, now()->subMonthNoOverflow());

        if ($lastMonth['net'] == 0.0) {
            return null;
        }

        $change = round((($thisMonth['net'] - $lastMonth['net']) / abs($lastMonth['net'])) * 100, 1);

        // Only worth surfacing once it's a real swing, not noise.
        if (abs($change) < 5) {
            return null;
        }

        $direction = $change > 0 ? 'increased' : 'decreased';
        $emoji = $change > 0 ? '📈' : '📉';

        return $this->upsert(
            $user,
            type: 'savings_trend',
            title: 'Savings Update',
            message: "{$emoji} Your net savings {$direction} by ".number_format(abs($change), 1).'% compared to last month.',
            actionUrl: route('reports.index'),
        );
    }

    /**
     * Alert for any active recurring transaction due today or tomorrow,
     * so the user isn't surprised by it.
     */
    public function checkUpcomingRecurring(User $user): array
    {
        $upcoming = $user->recurringTransactions()
            ->where('is_active', true)
            ->whereDate('next_due_date', '<=', Carbon::tomorrow())
            ->whereDate('next_due_date', '>=', Carbon::today())
            ->get();

        $alerts = [];
        foreach ($upcoming as $item) {
            $when = $item->next_due_date->isToday() ? 'today' : 'tomorrow';
            $label = $item->description ?: ucfirst($item->type);

            $alerts[] = $this->upsert(
                $user,
                type: 'recurring_due',
                title: "Upcoming {$item->type}: {$label}",
                message: "🔔 Your {$label} of ".number_format((float) $item->amount, 2)." is due {$when}.",
                actionUrl: route('recurring-transactions.show', $item->id),
                dedupeKey: (string) $item->id,
            );
        }

        return $alerts;
    }

    /**
     * Create the alert, or refresh an existing unread one of the same
     * type (+ optional dedupe key) instead of spamming duplicates.
     */
    private function upsert(User $user, string $type, string $title, string $message, ?string $actionUrl = null, ?string $dedupeKey = null): Notification
    {
        $query = Notification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_read', false);

        if ($dedupeKey !== null) {
            $query->where('title', $title);
        }

        $existing = $query->first();

        if ($existing) {
            $existing->update(['message' => $message]);
            return $existing;
        }

        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'is_read' => false,
        ]);
    }
}
