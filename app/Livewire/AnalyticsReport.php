<?php

namespace App\Livewire;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class AnalyticsReport extends Component
{
    public string $timeframe = '3months'; // 'month', '3months', '6months', 'year'

    #[On('transaction-saved')]
    public function refreshAnalytics(): void
    {
        // Re-render on updates
    }

    public function render()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Determine date boundary
        $startDate = match ($this->timeframe) {
            'month' => $now->copy()->startOfMonth(),
            '3months' => $now->copy()->subMonths(2)->startOfMonth(),
            '6months' => $now->copy()->subMonths(5)->startOfMonth(),
            'year' => $now->copy()->startOfYear(),
            default => $now->copy()->subMonths(2)->startOfMonth(),
        };

        $endDate = $now->copy()->endOfDay();

        // Aggregated totals in period
        $totalIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

        $totalExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

        $netSavings = $totalIncome - $totalExpense;
        $savingsRate = $totalIncome > 0 ? max(0, round(($netSavings / $totalIncome) * 100, 1)) : 0;

        $daysInPeriod = max(1, $startDate->diffInDays($endDate));
        $avgDailySpend = round($totalExpense / $daysInPeriod, 2);

        // Category Expenses Breakdown
        $categoryExpenses = Transaction::query()
            ->select('categories.name', 'categories.color_hex', DB::raw('SUM(transactions.amount) as total'))
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->whereBetween('transactions.transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('categories.id', 'categories.name', 'categories.color_hex')
            ->orderByDesc('total')
            ->get();

        // Category Income Breakdown
        $categoryIncome = Transaction::query()
            ->select('categories.name', 'categories.color_hex', DB::raw('SUM(transactions.amount) as total'))
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.user_id', $user->id)
            ->where('transactions.type', 'income')
            ->whereBetween('transactions.transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('categories.id', 'categories.name', 'categories.color_hex')
            ->orderByDesc('total')
            ->get();

        // Month-by-month trend (last 6 months)
        $monthLabels = [];
        $monthlyIncomes = [];
        $monthlyExpenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $mKey = $m->format('Y-m');
            $monthLabels[] = $m->format('M Y');

            $inc = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->where('transaction_date', 'like', "{$mKey}%")
                ->sum('amount');

            $exp = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->where('transaction_date', 'like', "{$mKey}%")
                ->sum('amount');

            $monthlyIncomes[] = $inc;
            $monthlyExpenses[] = $exp;
        }

        // Top Payees / Merchants
        $topMerchants = Transaction::query()
            ->select('payee_merchant', DB::raw('COUNT(*) as tx_count'), DB::raw('SUM(amount) as total_spent'))
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereNotNull('payee_merchant')
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('payee_merchant')
            ->orderByDesc('total_spent')
            ->take(6)
            ->get();

        return view('livewire.analytics-report', [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netSavings' => $netSavings,
            'savingsRate' => $savingsRate,
            'avgDailySpend' => $avgDailySpend,
            'categoryExpenses' => $categoryExpenses,
            'categoryIncome' => $categoryIncome,
            'monthLabels' => $monthLabels,
            'monthlyIncomes' => $monthlyIncomes,
            'monthlyExpenses' => $monthlyExpenses,
            'topMerchants' => $topMerchants,
        ]);
    }
}
