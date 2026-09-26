<div class="space-y-6">
    <!-- Top Greeting & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Financial Overview
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ now()->format('F Y') }} &bull; Keep track of your daily flow and budgets
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                x-on:click="$dispatch('open-quick-add', { type: 'expense' })"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm transition hover:shadow-red-500/20"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Expense</span>
            </button>
            <button
                type="button"
                x-on:click="$dispatch('open-quick-add', { type: 'income' })"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition hover:shadow-emerald-500/20"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Income</span>
            </button>
            <button
                type="button"
                x-on:click="$dispatch('open-quick-add', { type: 'transfer' })"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-xl transition"
            >
                <x-icon name="arrow-path" class="w-4 h-4" />
                <span class="hidden sm:inline">Transfer</span>
            </button>
        </div>
    </div>

    <!-- 4 Main Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Net Worth Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 p-5 text-white shadow-lg shadow-indigo-500/10">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium uppercase tracking-wider text-indigo-200">Total Net Worth</span>
                <span class="p-2 rounded-xl bg-white/10 backdrop-blur-sm">
                    <x-icon name="wallet" class="w-5 h-5 text-white" />
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black tracking-tight">
                    {{ auth()->user()->formatMoney($netWorth) }}
                </div>
                <div class="mt-1 flex items-center text-xs text-indigo-200">
                    <span>Across {{ $wallets->count() }} active accounts</span>
                </div>
            </div>
        </div>

        <!-- Monthly Income Card -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Monthly Inflow</span>
                <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="arrow-trending-up" class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                    {{ auth()->user()->formatMoney($monthlyIncome) }}
                </div>
                <div class="mt-1 flex items-center text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                    <span>Deposits & Earnings this month</span>
                </div>
            </div>
        </div>

        <!-- Monthly Expenses Card -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Monthly Outflow</span>
                <span class="p-2 rounded-xl bg-red-50 dark:bg-red-950 text-red-600 dark:text-red-400">
                    <x-icon name="arrow-trending-down" class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                    {{ auth()->user()->formatMoney($monthlyExpense) }}
                </div>
                <div class="mt-1 flex items-center text-xs text-red-600 dark:text-red-400 font-medium">
                    <span>Total spending this month</span>
                </div>
            </div>
        </div>

        <!-- Net Savings Rate Card -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Net Savings</span>
                <span class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400">
                    <x-icon name="trending-up" class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black {{ $netSavings >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400' }} tracking-tight">
                    {{ auth()->user()->formatMoney($netSavings) }}
                </div>
                <div class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-bold text-indigo-600 dark:text-indigo-400 mr-1">{{ $savingsRate }}%</span> savings rate
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 7-Day Cash Flow Chart -->
        <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60"
             x-data="{
                initChart() {
                    if (!window.Chart) return;
                    const ctx = this.$refs.cashFlowCanvas.getContext('2d');
                    if (this.chart) this.chart.destroy();
                    this.chart = new window.Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {{ json_encode($dayLabels) }},
                            datasets: [
                                {
                                    label: 'Income',
                                    data: {{ json_encode($dailyIncome) }},
                                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                    borderRadius: 6,
                                },
                                {
                                    label: 'Expense',
                                    data: {{ json_encode($dailyExpense) }},
                                    backgroundColor: 'rgba(239, 68, 68, 0.85)',
                                    borderRadius: 6,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: { boxWidth: 12, usePointStyle: true }
                                }
                            },
                            scales: {
                                y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.1)' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
             }"
             x-init="initChart()"
             x-on:transaction-saved.window="initChart()"
        >
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Recent Cash Flow</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Income vs Expenses over the past 7 days</p>
                </div>
                <a href="{{ route('analytics') }}" wire:navigate class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    View full analytics &rarr;
                </a>
            </div>
            <div class="h-64 relative">
                <canvas x-ref="cashFlowCanvas"></canvas>
            </div>
        </div>

        <!-- Category Breakdown Donut Chart -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60"
             x-data="{
                initDonut() {
                    if (!window.Chart) return;
                    const ctx = this.$refs.donutCanvas.getContext('2d');
                    if (this.chart) this.chart.destroy();
                    const totals = {{ json_encode($categoryTotals) }};
                    if (totals.length === 0) return;
                    this.chart = new window.Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: {{ json_encode($categoryLabels) }},
                            datasets: [{
                                data: totals,
                                backgroundColor: {{ json_encode($categoryColors) }},
                                borderWidth: 2,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { boxWidth: 10, font: { size: 10 } }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }
             }"
             x-init="initDonut()"
             x-on:transaction-saved.window="initDonut()"
        >
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Expense Distribution</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">By category this month</p>
                </div>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                @if(count($categoryTotals) > 0)
                    <canvas x-ref="donutCanvas"></canvas>
                @else
                    <div class="text-center text-xs text-gray-400 py-12">
                        No expenses logged for this month yet.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Budgets & Wallets Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Budget Health Meters (2 columns) -->
        <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Monthly Budgets Status</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Spending limits for {{ now()->format('F Y') }}</p>
                </div>
                <a href="{{ route('budgets') }}" wire:navigate class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Manage budgets &rarr;
                </a>
            </div>

            @if($budgets->isEmpty())
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No monthly budgets configured yet.</p>
                    <a href="{{ route('budgets') }}" wire:navigate class="inline-block mt-2 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                        + Set up category limits
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($budgets->take(4) as $budget)
                        @php
                            $spent = $budget->spent_amount;
                            $pct = $budget->percentage_used;
                            $limit = (float) $budget->limit_amount;
                            $isOver = $budget->is_over_budget;
                            $isNear = $budget->is_near_limit;
                        @endphp
                        <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-700/20">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $budget->category->color_hex ?? '#6366f1' }}"></span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $budget->category->name }}</span>
                                </div>
                                <span class="text-xs font-bold {{ $isOver ? 'text-red-600 dark:text-red-400' : ($isNear ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                                    {{ $pct }}%
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden mb-2">
                                <div
                                    class="h-full rounded-full transition-all duration-500 {{ $isOver ? 'bg-red-500' : ($isNear ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                    style="width: {{ min(100, $pct) }}%"
                                ></div>
                            </div>

                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>Spent: {{ auth()->user()->formatMoney($spent) }}</span>
                                <span>Limit: {{ auth()->user()->formatMoney($limit) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Wallets Quick View (1 column) -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Your Wallets</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Active accounts</p>
                </div>
                <a href="{{ route('wallets') }}" wire:navigate class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    View all &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @foreach($wallets->take(4) as $wallet)
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background-color: {{ $wallet->color_hex }}">
                                <x-icon :name="$wallet->icon" class="w-4 h-4" />
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $wallet->name }}</div>
                                <div class="text-xs text-gray-400">{{ $wallet->type_label }}</div>
                            </div>
                        </div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ auth()->user()->formatMoney($wallet->balance) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Transactions Ledger -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Recent Transactions</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Latest activity across all accounts</p>
            </div>
            <a href="{{ route('transactions') }}" wire:navigate class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                View all transactions &rarr;
            </a>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
            @forelse($recentTransactions as $tx)
                <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white"
                             style="background-color: {{ $tx->category ? $tx->category->color_hex : ($tx->type === 'transfer' ? '#6366f1' : '#10b981') }}">
                            @if($tx->type === 'transfer')
                                <x-icon name="arrow-path" class="w-5 h-5" />
                            @elseif($tx->category)
                                <x-icon :name="$tx->category->icon" class="w-5 h-5" />
                            @else
                                <x-icon name="tag" class="w-5 h-5" />
                            @endif
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <span>{{ $tx->payee_merchant ?: ($tx->type === 'transfer' ? 'Transfer' : ($tx->category->name ?? 'Uncategorized')) }}</span>
                                @if($tx->receipt_path)
                                    <span class="inline-flex items-center text-[10px] bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 px-1.5 py-0.5 rounded">
                                        Receipt
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400 flex items-center gap-2 mt-0.5">
                                <span>{{ $tx->transaction_date->format('M d, Y') }}</span>
                                &bull;
                                <span>{{ $tx->wallet->name }}</span>
                                @if($tx->type === 'transfer' && $tx->toWallet)
                                    <span>&rarr; {{ $tx->toWallet->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-sm font-bold {{ $tx->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : ($tx->type === 'expense' ? 'text-red-600 dark:text-red-400' : 'text-indigo-600 dark:text-indigo-400') }}">
                            {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '-' : '') }}{{ auth()->user()->formatMoney($tx->amount) }}
                        </div>
                        <div class="text-[11px] text-gray-400 uppercase tracking-wider">
                            {{ $tx->type }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-sm text-gray-400">
                    No transactions recorded yet. Click "Expense" or "Income" above to record one!
                </div>
            @endforelse
        </div>
    </div>
</div>
