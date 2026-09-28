<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Turns due recurring transactions into real Expense/Income records
// every day, so they actually show up in totals, reports, and budgets.
Schedule::command('finance:process-recurring')->daily();

// Generates the next month's budget for any budget marked "recurring",
// once its current period has ended — otherwise a budget only ever
// covered the one period it was created for.
Schedule::command('finance:process-recurring-budgets')->daily();

// Re-checks budget thresholds daily so alerts stay current even on
// days the user doesn't manually add an expense.
Schedule::call(function () {
    $budgetAlerts = app(\App\Services\BudgetAlertService::class);
    $insightAlerts = app(\App\Services\InsightAlertService::class);
    \App\Models\User::query()->each(function ($user) use ($budgetAlerts, $insightAlerts) {
        $budgetAlerts->checkUserBudgets($user);
        $insightAlerts->checkSavingsTrend($user);
        $insightAlerts->checkUpcomingRecurring($user);
    });
})->daily();
