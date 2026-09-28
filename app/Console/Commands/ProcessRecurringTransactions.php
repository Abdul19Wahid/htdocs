<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\Income;
use App\Models\RecurringTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Recurring transactions were previously just reminders sitting in a
 * table — next_due_date would pass and nothing happened: no Expense or
 * Income row got created, so they never showed up in totals, reports,
 * or budgets. This command is what actually makes them real.
 *
 * Run daily (see routes/console.php schedule). For every active recurring
 * transaction whose next_due_date is today or earlier, it creates the
 * matching Expense/Income record(s) and advances next_due_date to the
 * next occurrence — catching up on any missed days if the schedule
 * wasn't run for a while, without creating duplicates.
 */
class ProcessRecurringTransactions extends Command
{
    protected $signature = 'finance:process-recurring';

    protected $description = 'Generate Expense/Income records for any due recurring transactions';

    public function handle(): int
    {
        $due = RecurringTransaction::where('is_active', true)
            ->whereDate('next_due_date', '<=', Carbon::today())
            ->get();

        $created = 0;

        foreach ($due as $recurring) {
            // Catch up on every missed occurrence, not just one, so a
            // recurring item doesn't silently fall behind if the
            // schedule was skipped for a while.
            while (
                $recurring->is_active
                && $recurring->next_due_date
                && $recurring->next_due_date->lte(Carbon::today())
                && (! $recurring->end_date || $recurring->next_due_date->lte($recurring->end_date))
            ) {
                $this->materialize($recurring);
                $created++;

                $recurring->next_due_date = $this->nextOccurrence($recurring->next_due_date, $recurring->frequency);

                if ($recurring->end_date && $recurring->next_due_date->gt($recurring->end_date)) {
                    $recurring->is_active = false;
                }

                $recurring->save();
            }
        }

        $this->info("Processed {$due->count()} recurring transaction(s), created {$created} record(s).");

        return self::SUCCESS;
    }

    private function materialize(RecurringTransaction $recurring): void
    {
        $data = [
            'user_id' => $recurring->user_id,
            'payment_method_id' => $recurring->payment_method_id,
            'amount' => $recurring->amount,
            'description' => $recurring->description,
            'date' => $recurring->next_due_date->toDateString(),
            'notes' => 'Auto-generated from recurring transaction.',
        ];

        if ($recurring->type === 'expense') {
            Expense::create($data + ['category_id' => $recurring->category_id]);
        } else {
            Income::create($data + ['income_source_id' => $recurring->income_source_id]);
        }
    }

    private function nextOccurrence(Carbon $from, string $frequency): Carbon
    {
        return match ($frequency) {
            'daily' => $from->copy()->addDay(),
            'weekly' => $from->copy()->addWeek(),
            'monthly' => $from->copy()->addMonthNoOverflow(),
            'yearly' => $from->copy()->addYearNoOverflow(),
            default => $from->copy()->addMonthNoOverflow(),
        };
    }
}
