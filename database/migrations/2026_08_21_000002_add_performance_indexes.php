<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * None of the original tables indexed the columns actually driving this
 * app's most-hit queries. Every dashboard load, report, budget spent-
 * calculation, and transaction list filters by (user_id + date range) on
 * expenses/incomes — with only user_id indexed (via the foreign key),
 * MySQL has to scan every row for that user and check dates one by one.
 * Fine at a handful of rows; it degrades as real usage builds up. Same
 * story for budgets (recurring-budget generator scans by is_recurring +
 * end_date) and notifications (dashboard alerts query by is_read).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['user_id', 'date']);
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->index(['user_id', 'date']);
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->index(['user_id', 'start_date', 'end_date']);
            $table->index(['is_recurring', 'end_date']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'date']);
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'date']);
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'start_date', 'end_date']);
            $table->dropIndex(['is_recurring', 'end_date']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read']);
        });
    }
};
