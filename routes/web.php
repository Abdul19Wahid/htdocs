<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\IncomeSourceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : view('welcome'))->name('home');

// TEMPORARY — diagnostic route to force an opcache reset via Laravel's own
// router (a standalone .php file in public/ doesn't work here since every
// request is being routed through Laravel first). Remove this route and
// re-upload web.php once you've used it once.
Route::get('/__opcache-reset-temp', function () {
    if (function_exists('opcache_reset')) {
        opcache_reset();
        return 'OPcache reset done.';
    }
    return 'opcache_reset() is not available on this server — this was not the cause.';
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    Route::resource('incomes', IncomeController::class);
    Route::resource('expenses', ExpenseController::class);

    Route::resource('categories', CategoryController::class);
    Route::resource('income-sources', IncomeSourceController::class)->except(['show']);
    Route::resource('payment-methods', PaymentMethodController::class)->except(['show']);
    Route::resource('budgets', BudgetController::class);
    Route::resource('savings-goals', SavingsGoalController::class);
    Route::post('savings-goals/{savings_goal}/contribute', [SavingsGoalController::class, 'contribute'])->name('savings-goals.contribute');
    Route::resource('recurring-transactions', RecurringTransactionController::class);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [ReportController::class, 'csv'])->name('reports.csv');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
