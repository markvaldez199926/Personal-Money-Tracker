<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Monthly Budgets
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Set category spending limits and monitor budget health in real-time
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Month Selector -->
            <input
                type="month"
                wire:model.live="selected_month"
                class="rounded-xl border-gray-200 dark:border-gray-700 py-1.5 px-3 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 shadow-sm"
            />

            <button
                wire:click="copyPreviousMonthBudgets"
                wire:confirm="Copy all budget limits from the previous month to this month?"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl shadow-sm transition"
                title="Copy previous month limits"
            >
                <x-icon name="arrow-path" class="w-3.5 h-3.5" />
                <span class="hidden sm:inline">Copy Last Month</span>
            </button>

            <button
                wire:click="openNewBudgetModal"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition hover:shadow-indigo-500/20"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Set Budget</span>
            </button>
        </div>
    </div>

    <!-- Aggregate Budget Overview Card -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Overall Monthly Budget Consumption</span>
                <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">
                    {{ auth()->user()->formatMoney($totalSpent) }}
                    <span class="text-base font-normal text-gray-400">/ {{ auth()->user()->formatMoney($totalBudgeted) }}</span>
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs text-gray-400">Remaining Cushion</span>
                <div class="text-xl font-bold {{ $totalRemaining > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">
                    {{ auth()->user()->formatMoney($totalRemaining) }}
                </div>
            </div>
        </div>

        <!-- Master Progress Bar -->
        <div class="w-full bg-gray-100 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
            <div
                class="h-full rounded-full transition-all duration-500 {{ $overallPercentage >= 100 ? 'bg-red-500' : ($overallPercentage >= 80 ? 'bg-amber-500' : 'bg-indigo-600') }}"
                style="width: {{ min(100, $overallPercentage) }}%"
            ></div>
        </div>

        <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 mt-2">
            <span>{{ $overallPercentage }}% spent</span>
            <span>Target Month: {{ \Carbon\Carbon::createFromFormat('Y-m', $selected_month)->format('F Y') }}</span>
        </div>
    </div>

    <!-- Category Budgets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($budgets as $budget)
            @php
                $spent = $budget->spent_amount;
                $pct = $budget->percentage_used;
                $limit = (float) $budget->limit_amount;
                $isOver = $budget->is_over_budget;
                $isNear = $budget->is_near_limit;
            @endphp
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60 relative overflow-hidden transition hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background-color: {{ $budget->category->color_hex ?? '#6366f1' }}">
                            <x-icon :name="$budget->category->icon ?? 'tag'" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gray-900 dark:text-white">{{ $budget->category->name }}</h3>
                            <span class="text-xs text-gray-400">Limit: {{ auth()->user()->formatMoney($limit) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <button
                            wire:click="openEditBudgetModal({{ $budget->id }})"
                            class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                            title="Edit Limit"
                        >
                            <x-icon name="pencil" class="w-4 h-4" />
                        </button>
                        <button
                            wire:click="deleteBudget({{ $budget->id }})"
                            wire:confirm="Remove this category budget?"
                            class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                            title="Delete Budget"
                        >
                            <x-icon name="trash" class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Status Badges -->
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Spent: {{ auth()->user()->formatMoney($spent) }}</span>
                    @if($isOver)
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300">
                            Over by {{ auth()->user()->formatMoney($spent - $limit) }}
                        </span>
                    @elseif($isNear)
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300">
                            Warning &bull; {{ $pct }}%
                        </span>
                    @else
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
                            Safe &bull; {{ $pct }}%
                        </span>
                    @endif
                </div>

                <!-- Progress Bar -->
                <div class="mt-2 w-full bg-gray-100 dark:bg-gray-700 h-2.5 rounded-full overflow-hidden">
                    <div
                        class="h-full rounded-full transition-all duration-500 {{ $isOver ? 'bg-red-500' : ($isNear ? 'bg-amber-500' : 'bg-emerald-500') }}"
                        style="width: {{ min(100, $pct) }}%"
                    ></div>
                </div>

                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs text-gray-400">
                    <span>Remaining: {{ auth()->user()->formatMoney($budget->remaining_amount) }}</span>
                    <span>Alert at {{ (int)$budget->alert_threshold_percentage }}%</span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">No category budgets defined for this month yet.</p>
                <button
                    wire:click="openNewBudgetModal"
                    class="mt-3 px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl"
                >
                    + Add Your First Budget
                </button>
            </div>
        @endforelse
    </div>

    <!-- Set / Edit Budget Modal -->
    @if($showBudgetModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" wire:click="$set('showBudgetModal', false)"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">
                            {{ $editingBudgetId ? 'Edit Budget Limit' : 'Configure Category Budget' }}
                        </h3>
                        <button wire:click="$set('showBudgetModal', false)" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="plus" class="w-5 h-5 rotate-45" />
                        </button>
                    </div>

                    <form wire:submit="saveBudget" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Expense Category</label>
                            <select
                                wire:model="category_id"
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                                {{ $editingBudgetId ? 'disabled' : '' }}
                            >
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Monthly Spending Limit</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-sm font-bold text-gray-400">{{ auth()->user()->currency_symbol ?? '$' }}</span>
                                </div>
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="limit_amount"
                                    placeholder="500.00"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 pl-8 pr-3 py-2 text-sm font-bold text-gray-900 dark:text-white dark:bg-gray-700/60"
                                />
                            </div>
                            @error('limit_amount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Warning Threshold Percentage ({{ $alert_threshold_percentage }}%)
                            </label>
                            <input
                                type="range"
                                min="50"
                                max="100"
                                step="5"
                                wire:model.live="alert_threshold_percentage"
                                class="w-full h-2 bg-gray-200 rounded-lg cursor-pointer dark:bg-gray-700"
                            />
                            <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                                <span>50%</span>
                                <span>80% (Recommended)</span>
                                <span>100%</span>
                            </div>
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100 dark:border-gray-700">
                            <button
                                type="button"
                                wire:click="$set('showBudgetModal', false)"
                                class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 rounded-lg"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm"
                            >
                                Save Budget
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
