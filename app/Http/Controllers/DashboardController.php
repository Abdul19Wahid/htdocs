<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Notification;
use App\Services\BudgetAlertService;
use App\Services\FinanceCalculator;
use App\Services\InsightAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    private BudgetAlertService $budgetAlertService;
    private InsightAlertService $insightAlertService;
    private FinanceCalculator $calc;

    public function __construct(BudgetAlertService $budgetAlertService, InsightAlertService $insightAlertService, FinanceCalculator $calc)
    {
        $this->budgetAlertService = $budgetAlertService;
        $this->insightAlertService = $insightAlertService;
        $this->calc = $calc;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        // InfinityFree's free-tier cron isn't reliable enough to depend on
        // for alerts, so re-check on dashboard load too — throttled to once
        // per user per day (cache-based) so it doesn't add a query burden
        // to every single page view.
        Cache::remember("alerts-checked:{$user->id}:".today()->toDateString(), 3600, function () use ($user) {
            $this->budgetAlertService->checkUserBudgets($user);
            $this->insightAlertService->checkSavingsTrend($user);
            $this->insightAlertService->checkUpcomingRecurring($user);

            // Same InfinityFree-cron-isn't-reliable reasoning as above:
            // generate this user's next recurring budget period on
            // dashboard load too, not just via the daily scheduled command.
            \App\Models\Budget::where('user_id', $user->id)
                ->where('is_recurring', true)
                ->whereDate('end_date', '<', today())
                ->get()
                ->each(function ($budget) use ($user) {
                    $nextStart = $budget->end_date->copy()->addDay();
                    $exists = \App\Models\Budget::where('user_id', $user->id)
                        ->where('category_id', $budget->category_id)
                        ->where('name', $budget->name)
                        ->whereDate('start_date', $nextStart->toDateString())
                        ->exists();
                    if (! $exists) {
                        \App\Models\Budget::create([
                            'user_id' => $user->id,
                            'category_id' => $budget->category_id,
                            'name' => $budget->name,
                            'amount' => $budget->amount,
                            'start_date' => $nextStart,
                            'end_date' => $nextStart->copy()->endOfMonth(),
                            'alert_percentage' => $budget->alert_percentage,
                            'is_recurring' => true,
                        ]);
                    }
                });

            return true;
        });

        $totalIncome = $this->calc->incomeTotal($user);
        $totalExpenses = $this->calc->expenseTotal($user);
        $monthlyTotals = $this->calc->monthlyTotals($user);
        $monthlyIncome = $monthlyTotals['income'];
        $monthlyExpenses = $monthlyTotals['expenses'];
        $todayExpenses = $this->calc->expenseTotal($user, today()->toDateString(), today()->toDateString());

        $budgets = $user->budgets()->with('category')->whereDate('start_date', '<=', today())->whereDate('end_date', '>=', today())->get();
        $budgetTotal = (float) $budgets->sum('amount');
        $budgetSpent = $this->budgetSpent($user, $budgets);
        $budgetPercent = $budgetTotal > 0 ? min(round(($budgetSpent / $budgetTotal) * 100, 1), 100) : 0;
        $recentExpenses = $user->expenses()->with(['category', 'paymentMethod'])->latest('date')->latest('id')->limit(7)->get();
        $recentIncome = $user->incomes()->with(['incomeSource', 'paymentMethod'])->latest('date')->latest('id')->limit(5)->get();
        $goals = $user->savingsGoals()->where('status', 'active')->latest()->limit(5)->get();
        $unreadNotifications = $user->financialNotifications()->where('is_read', false)->count();

        // Surface every alert type together (budget thresholds, savings
        // trend, upcoming recurring items), not just budget alerts.
        $budgetAlerts = $user->financialNotifications()
            ->whereIn('type', ['budget_alert', 'budget_exceeded', 'savings_trend', 'recurring_due'])
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->get();

        $chart = $this->calc->monthlySeries($user, 6)->map(fn ($m) => [
            'label' => $m['label'],
            'income' => $m['income'],
            'expense' => $m['expenses'],
        ]);

        // Note: kept as a direct query (not FinanceCalculator::categoryBreakdown)
        // because this view needs ->category->name/color objects, while the
        // calculator returns a simple name=>total map for reports/summaries.
        $categoryBreakdown = $user->expenses()
            ->selectRaw('category_id, SUM(amount) as total')
            ->with('category:id,name,color')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        return view('dashboard', compact(
            'totalIncome', 'totalExpenses', 'monthlyIncome', 'monthlyExpenses', 'todayExpenses',
            'budgetTotal', 'budgetSpent', 'budgetPercent', 'recentExpenses', 'recentIncome',
            'goals', 'chart', 'categoryBreakdown', 'unreadNotifications', 'budgetAlerts'
        ));
    }

    private function budgetSpent($user, $budgets): float
    {
        if ($budgets->isEmpty()) return 0;

        // Was previously one whereBetween()->sum() query PER budget — for
        // a user with 5 active budgets, that's 5 extra queries on every
        // single dashboard load. Fetch the relevant window's expenses once
        // and sum per-budget in PHP instead.
        $earliestStart = $budgets->min('start_date');
        $latestEnd = $budgets->max('end_date');

        $expenses = $user->expenses()
            ->whereBetween('date', [$earliestStart, $latestEnd])
            ->get(['category_id', 'date', 'amount']);

        $spent = 0;
        foreach ($budgets as $budget) {
            $matching = $expenses->filter(function ($e) use ($budget) {
                if ($e->date->lt($budget->start_date) || $e->date->gt($budget->end_date)) {
                    return false;
                }
                return ! $budget->category_id || $e->category_id === $budget->category_id;
            });
            $spent += (float) $matching->sum('amount');
        }

        return $spent;
    }
}
