<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class BudgetAlertService
{
    /**
     * Check all budgets for the user and create alerts if thresholds are exceeded.
     */
    public function checkUserBudgets(User $user): array
    {
        $alerts = [];
        $budgets = $user->budgets()
            ->where('end_date', '>=', Carbon::today())
            ->get();

        foreach ($budgets as $budget) {
            $spent = $this->getBudgetSpent($budget);
            $percentage = $this->getSpendingPercentage($spent, $budget->amount);
            $threshold = $budget->alert_percentage ?? 80;

            if ($percentage >= $threshold) {
                $alert = $this->createOrUpdateAlert($budget, $spent, $percentage);
                $alerts[] = $alert;
            }
        }

        return $alerts;
    }

    /**
     * Check a specific budget and create alert if needed.
     */
    public function checkBudget(Budget $budget): ?Notification
    {
        $spent = $this->getBudgetSpent($budget);
        $percentage = $this->getSpendingPercentage($spent, $budget->amount);
        $threshold = $budget->alert_percentage ?? 80;

        if ($percentage >= $threshold) {
            return $this->createOrUpdateAlert($budget, $spent, $percentage);
        }

        return null;
    }

    /**
     * Get total amount spent against a budget.
     */
    public function getBudgetSpent(Budget $budget): float
    {
        $query = Expense::where('user_id', $budget->user_id)
            ->whereBetween('date', [$budget->start_date, $budget->end_date]);

        if ($budget->category_id) {
            $query->where('category_id', $budget->category_id);
        }

        return (float) $query->sum('amount');
    }

    /**
     * Get spending percentage.
     */
    public function getSpendingPercentage(float $spent, float $budget): float
    {
        if ($budget <= 0) {
            return 0;
        }
        return ($spent / $budget) * 100;
    }

    /**
     * Create or update a budget alert notification.
     */
    private function createOrUpdateAlert(Budget $budget, float $spent, float $percentage): Notification
    {
        $type = $percentage >= 100 ? 'budget_exceeded' : 'budget_alert';
        
        $message = match(true) {
            $percentage >= 100 => "Budget exceeded: {$budget->name} ({$percentage}% spent)",
            $percentage >= 90 => "Budget almost exceeded: {$budget->name} ({$percentage}% spent)",
            default => "Budget approaching limit: {$budget->name} ({$percentage}% spent)",
        };

        // Check if alert already exists and update it
        $notification = Notification::where('user_id', $budget->user_id)
            ->where('type', $type)
            ->where('title', "Budget Alert: {$budget->name}")
            ->where('is_read', false)
            ->first();

        if ($notification) {
            $notification->update([
                'message' => $message . " - " . now()->format('H:i'),
            ]);
        } else {
            $notification = Notification::create([
                'user_id' => $budget->user_id,
                'type' => $type,
                'title' => "Budget Alert: {$budget->name}",
                'message' => $message,
                'action_url' => route('budgets.show', $budget->id),
                'is_read' => false,
            ]);
        }

        return $notification;
    }

    /**
     * Get all active budget alerts for a user.
     */
    public function getActiveBudgetAlerts(User $user)
    {
        return Notification::where('user_id', $user->id)
            ->whereIn('type', ['budget_alert', 'budget_exceeded'])
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Clear expired budget alerts.
     */
    public function clearExpiredAlerts(User $user): int
    {
        $budgetIds = $user->budgets()
            ->where('end_date', '<', Carbon::today())
            ->pluck('id');

        return Notification::where('user_id', $user->id)
            ->whereIn('type', ['budget_alert', 'budget_exceeded'])
            ->whereJsonContains('data->budget_id', $budgetIds)
            ->update(['is_read' => true]);
    }
}
