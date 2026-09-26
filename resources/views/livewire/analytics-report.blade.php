<div class="space-y-6">
    <!-- Header & Timeframe Pills -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Financial Analytics & Insights
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Visual analysis of cash flow trends, category breakdown, and top spending
            </p>
        </div>

        <!-- Timeframe selector -->
        <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <button
                type="button"
                wire:click="$set('timeframe', 'month')"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $timeframe === 'month' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
            >
                This Month
            </button>
            <button
                type="button"
                wire:click="$set('timeframe', '3months')"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $timeframe === '3months' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
            >
                3 Months
            </button>
            <button
                type="button"
                wire:click="$set('timeframe', '6months')"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $timeframe === '6months' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
            >
                6 Months
            </button>
            <button
                type="button"
                wire:click="$set('timeframe', 'year')"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $timeframe === 'year' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
            >
                This Year
            </button>
        </div>
    </div>

    <!-- 4 Ratios / Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Inflow -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Income</span>
            <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2">
                {{ auth()->user()->formatMoney($totalIncome) }}
            </div>
            <p class="text-xs text-gray-400 mt-1">Earnings across all wallets</p>
        </div>

        <!-- Total Outflow -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Spent</span>
            <div class="text-2xl sm:text-3xl font-black text-red-600 dark:text-red-400 mt-2">
                {{ auth()->user()->formatMoney($totalExpense) }}
            </div>
            <p class="text-xs text-gray-400 mt-1">Discretionary & fixed spending</p>
        </div>

        <!-- Net Savings Rate -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Net Surplus</span>
            <div class="text-2xl sm:text-3xl font-black {{ $netSavings >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-red-600' }} mt-2">
                {{ auth()->user()->formatMoney($netSavings) }}
            </div>
            <p class="text-xs text-gray-400 mt-1 font-semibold">
                {{ $savingsRate }}% Savings Rate
            </p>
        </div>

        <!-- Daily Burn Rate -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Daily Burn Rate</span>
            <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-2">
                {{ auth()->user()->formatMoney($avgDailySpend) }}
            </div>
            <p class="text-xs text-gray-400 mt-1">Average daily expenditure</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 6-Month Income vs Expense Bar Chart -->
        <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60"
             x-data="{
                initTrendChart() {
                    if (!window.Chart) return;
                    const ctx = this.$refs.trendCanvas.getContext('2d');
                    if (this.chart) this.chart.destroy();
                    this.chart = new window.Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {{ json_encode($monthLabels) }},
                            datasets: [
                                {
                                    label: 'Income',
                                    data: {{ json_encode($monthlyIncomes) }},
                                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                    borderRadius: 6,
                                },
                                {
                                    label: 'Expense',
                                    data: {{ json_encode($monthlyExpenses) }},
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
             x-init="initTrendChart()"
             x-on:transaction-saved.window="initTrendChart()"
        >
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">6-Month Cash Flow Trend</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Monthly comparison of earnings against spending</p>
                </div>
            </div>
            <div class="h-72 relative">
                <canvas x-ref="trendCanvas"></canvas>
            </div>
        </div>

        <!-- Category Expenses Donut Chart -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60"
             x-data="{
                initExpenseDonut() {
                    if (!window.Chart) return;
                    const ctx = this.$refs.categoryDonut.getContext('2d');
                    if (this.chart) this.chart.destroy();
                    const totals = {{ json_encode($categoryExpenses->pluck('total')->map(fn($v)=>(float)$v)->toArray()) }};
                    if (totals.length === 0) return;
                    this.chart = new window.Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: {{ json_encode($categoryExpenses->pluck('name')->toArray()) }},
                            datasets: [{
                                data: totals,
                                backgroundColor: {{ json_encode($categoryExpenses->pluck('color_hex')->toArray()) }},
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
             x-init="initExpenseDonut()"
             x-on:transaction-saved.window="initExpenseDonut()"
        >
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Spending by Category</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Selected timeframe</p>
                </div>
            </div>
            <div class="h-72 relative flex items-center justify-center">
                @if($categoryExpenses->isNotEmpty())
                    <canvas x-ref="categoryDonut"></canvas>
                @else
                    <div class="text-center text-xs text-gray-400 py-12">
                        No expense records in this timeframe.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Category Breakdown Table & Top Merchants -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category Detailed Breakdown -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-1">Expense Categories Detail</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Breakdown of spending and proportion</p>

            <div class="space-y-3">
                @forelse($categoryExpenses as $cat)
                    @php
                        $percentage = $totalExpense > 0 ? round(((float)$cat->total / $totalExpense) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $cat->color_hex }}"></span>
                                <span class="text-gray-900 dark:text-white">{{ $cat->name }}</span>
                            </div>
                            <div class="text-gray-900 dark:text-white">
                                {{ auth()->user()->formatMoney($cat->total) }}
                                <span class="text-gray-400 font-normal ml-1">({{ $percentage }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300" style="width: {{ $percentage }}%; background-color: {{ $cat->color_hex }}"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-6 text-center">No categories recorded.</p>
                @endforelse
            </div>
        </div>

        <!-- Top Merchants / Payees -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-1">Top Spending Merchants</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Where your money goes the most</p>

            <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                @forelse($topMerchants as $index => $m)
                    <div class="py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/30 transition px-2 rounded-xl">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $m->payee_merchant }}</div>
                                <div class="text-[11px] text-gray-400">{{ $m->tx_count }} transaction(s)</div>
                            </div>
                        </div>
                        <div class="text-xs font-bold text-red-600 dark:text-red-400">
                            {{ auth()->user()->formatMoney($m->total_spent) }}
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-6 text-center">No merchant data available in this timeframe.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
