<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // When true, a new budget for the following month is generated
            // automatically once this one's end_date passes — see
            // App\Console\Commands\ProcessRecurringBudgets. Without this,
            // a "Food" budget only ever covered one month and the user had
            // to recreate it by hand every month.
            $table->boolean('is_recurring')->default(false)->after('alert_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn('is_recurring');
        });
    }
};
