<?php

namespace App\Livewire;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardOverview extends Component
{
    #[On('transaction-saved')]
    public function refreshDashboard(): void
    {
        // Triggers re-render on transaction save
    }

    public function render()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $currentMonth = $now->format('Y-m');

        // Wallets
        $wallets = $user->wallets()->active()->get();
        $netWorth = $wallets->sum('balance');

        // Current Month Stats
        $monthlyIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->where('transaction_date', 'like', "{$currentMonth}%")
            ->sum('amount');

        $monthlyExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('transaction_date', 'like', "{$currentMonth}%")
            ->sum('amount');

        $netSavings = $monthlyIncome - $monthlyExpense;
        $savingsRate = $monthlyIncome > 0 ? max(0, round(($netSavings / $monthlyIncome) * 100, 1)) : 0;

        // Recent Transactions
        $recentTransactions = Transaction::with(['wallet', 'category', 'toWallet'])
            ->where('user_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(7)
            ->get();

        // Budgets for this month
        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month_year', $currentMonth)
            ->get();

        // Chart Data: Expense by Category (Current Month)
        $expenseByCategory = Transaction::query()
            ->select('categories.name', 'categories.color_hex', DB::raw('SUM(transactions.amount) as total'))
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->where('transactions.transaction_date', 'like', "{$currentMonth}%")
            ->groupBy('categories.id', 'categories.name', 'categories.color_hex')
            ->orderByDesc('total')
            ->get();

        // Chart Data: Last 7 Days Cash Flow
        $days = collect();
        $dailyIncome = [];
        $dailyExpense = [];
        $dayLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $dayStr = $day->toDateString();
            $dayLabels[] = $day->format('M d');

            $inc = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->where('transaction_date', $dayStr)
                ->sum('amount');

            $exp = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->where('transaction_date', $dayStr)
                ->sum('amount');

            $dailyIncome[] = $inc;
            $dailyExpense[] = $exp;
        }

        return view('livewire.dashboard-overview', [
            'netWorth' => $netWorth,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
            'netSavings' => $netSavings,
            'savingsRate' => $savingsRate,
            'wallets' => $wallets,
            'recentTransactions' => $recentTransactions,
            'budgets' => $budgets,
            'categoryLabels' => $expenseByCategory->pluck('name')->toArray(),
            'categoryTotals' => $expenseByCategory->pluck('total')->map(fn($v) => (float)$v)->toArray(),
            'categoryColors' => $expenseByCategory->pluck('color_hex')->toArray(),
            'dayLabels' => $dayLabels,
            'dailyIncome' => $dailyIncome,
            'dailyExpense' => $dailyExpense,
        ]);
    }
}
