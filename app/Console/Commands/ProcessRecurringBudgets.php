<?php

namespace App\Console\Commands;

use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Without this, a budget only ever covered the one period it was created
 * for — a "Food, GH₵500" budget set up for August would just vanish (no
 * alerts, no progress bar) come September unless the user manually
 * recreated it. Any budget with is_recurring=true gets a fresh copy for
 * the next calendar month once its end_date has passed, keeping the same
 * name/category/amount/alert threshold.
 */
class ProcessRecurringBudgets extends Command
{
    protected $signature = 'finance:process-recurring-budgets';

    protected $description = 'Create the next period for any recurring budget that has ended';

    public function handle(): int
    {
        $ended = Budget::where('is_recurring', true)
            ->whereDate('end_date', '<', Carbon::today())
            ->get();

        $created = 0;

        foreach ($ended as $budget) {
            // Don't create a duplicate if a budget already exists for the
            // immediate next period (covers the command running more than
            // once, or the user having already made one manually).
            $nextStart = $budget->end_date->copy()->addDay();

            $alreadyExists = Budget::where('user_id', $budget->user_id)
                ->where('category_id', $budget->category_id)
                ->where('name', $budget->name)
                ->whereDate('start_date', $nextStart->toDateString())
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            Budget::create([
                'user_id' => $budget->user_id,
                'category_id' => $budget->category_id,
                'name' => $budget->name,
                'amount' => $budget->amount,
                'start_date' => $nextStart,
                'end_date' => $nextStart->copy()->endOfMonth(),
                'alert_percentage' => $budget->alert_percentage,
                'is_recurring' => true,
            ]);

            $created++;
        }

        $this->info("Checked {$ended->count()} ended recurring budget(s), created {$created} new period(s).");

        return self::SUCCESS;
    }
}
