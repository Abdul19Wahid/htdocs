<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $type = $request->input('type', 'all');
        $search = trim((string) $request->input('search', ''));
        $from = $request->input('from');
        $to = $request->input('to');
        $sort = $request->input('sort', 'date_desc');

        $rows = collect();

        if ($type !== 'expense') {
            $incomeQuery = $user->incomes()->with(['incomeSource', 'paymentMethod']);
            if ($from) $incomeQuery->whereDate('date', '>=', $from);
            if ($to) $incomeQuery->whereDate('date', '<=', $to);
            if ($search) $incomeQuery->where(fn ($q) => $q->where('description', 'like', "%{$search}%")->orWhere('notes', 'like', "%{$search}%"));
            $rows = $rows->concat($incomeQuery->get()->map(fn ($item) => [
                'id' => $item->id,
                'type' => 'income',
                'date' => $item->date,
                'description' => $item->description ?: 'Income',
                'category' => $item->incomeSource?->name ?: 'Income',
                'payment' => $item->paymentMethod?->name ?: '—',
                'amount' => (float) $item->amount,
                'edit_url' => route('incomes.edit', $item),
                'delete_url' => route('incomes.destroy', $item),
            ]));
        }

        if ($type !== 'income') {
            $expenseQuery = $user->expenses()->with(['category', 'paymentMethod']);
            if ($from) $expenseQuery->whereDate('date', '>=', $from);
            if ($to) $expenseQuery->whereDate('date', '<=', $to);
            if ($search) $expenseQuery->where(fn ($q) => $q->where('description', 'like', "%{$search}%")->orWhere('notes', 'like', "%{$search}%"));
            $rows = $rows->concat($expenseQuery->get()->map(fn ($item) => [
                'id' => $item->id,
                'type' => 'expense',
                'date' => $item->date,
                'description' => $item->description ?: 'Expense',
                'category' => $item->category?->name ?: 'Uncategorized',
                'payment' => $item->paymentMethod?->name ?: '—',
                'amount' => (float) $item->amount,
                'edit_url' => route('expenses.edit', $item),
                'delete_url' => route('expenses.destroy', $item),
            ]));
        }

        $rows = match ($sort) {
            'date_asc' => $rows->sortBy(fn ($row) => $row['date']->timestamp),
            'amount_desc' => $rows->sortByDesc('amount'),
            'amount_asc' => $rows->sortBy('amount'),
            default => $rows->sortByDesc(fn ($row) => $row['date']->timestamp),
        };
        $rows = $rows->values();

        $totalIncome = (float) $rows->where('type', 'income')->sum('amount');
        $totalExpenses = (float) $rows->where('type', 'expense')->sum('amount');

        // The unified list merges two separate tables (incomes + expenses)
        // in PHP, so it can't use Eloquent's ->paginate() directly — every
        // matching row was previously loaded and rendered on one page,
        // which would only get slower as history builds up. Paginate the
        // already-sorted collection manually instead.
        $perPage = 25;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paged = new LengthAwarePaginator(
            $rows->forPage($page, $perPage),
            $rows->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('transactions.index', [
            'rows' => $paged,
            'type' => $type,
            'search' => $search,
            'from' => $from,
            'to' => $to,
            'sort' => $sort,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
        ]);
    }
}
