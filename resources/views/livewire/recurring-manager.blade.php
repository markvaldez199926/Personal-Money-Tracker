<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Recurring Bills & Incomes
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Automate your monthly subscriptions, bills, rent, and scheduled salary
            </p>
        </div>

        <button
            wire:click="openNewModal"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition hover:shadow-indigo-500/20"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Add Recurring Item</span>
        </button>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Monthly Fixed Outflow</span>
            <div class="text-2xl font-black text-red-600 dark:text-red-400 mt-1">
                {{ auth()->user()->formatMoney($monthlyExpenses) }}
            </div>
            <p class="text-xs text-gray-400 mt-0.5">Committed subscriptions & bills</p>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Monthly Fixed Inflow</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                {{ auth()->user()->formatMoney($monthlyIncome) }}
            </div>
            <p class="text-xs text-gray-400 mt-0.5">Guaranteed salary & retainers</p>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Net Predictable Cashflow</span>
            <div class="text-2xl font-black {{ ($monthlyIncome - $monthlyExpenses) >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-red-600' }} mt-1">
                {{ auth()->user()->formatMoney($monthlyIncome - $monthlyExpenses) }}
            </div>
            <p class="text-xs text-gray-400 mt-0.5">Surplus before discretionary spending</p>
        </div>
    </div>

    <!-- Recurring Items Table -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/75 dark:bg-gray-750 text-gray-400 uppercase text-[11px] font-semibold tracking-wider">
                        <th class="py-3.5 px-4 sm:px-6">Name</th>
                        <th class="py-3.5 px-4">Frequency</th>
                        <th class="py-3.5 px-4">Category & Wallet</th>
                        <th class="py-3.5 px-4">Next Due</th>
                        <th class="py-3.5 px-4 text-center">Auto-Post</th>
                        <th class="py-3.5 px-4 text-right">Amount</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($recurringItems as $item)
                        @php
                            $today = \Carbon\Carbon::today();
                            $dueDate = \Carbon\Carbon::parse($item->next_due_date);
                            $diffDays = $today->diffInDays($dueDate, false);
                        @endphp
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition {{ ! $item->is_active ? 'opacity-50' : '' }}">
                            <!-- Name -->
                            <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $item->name }}</div>
                                @if($item->notes)
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $item->notes }}</div>
                                @endif
                            </td>

                            <!-- Frequency -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 capitalize">
                                    {{ $item->frequency }}
                                </span>
                            </td>

                            <!-- Category & Wallet -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                <div>{{ $item->category?->name ?? 'Uncategorized' }}</div>
                                <div class="text-gray-400 text-[11px]">{{ $item->wallet->name }}</div>
                            </td>

                            <!-- Next Due -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900 dark:text-white">
                                    {{ $dueDate->format('M d, Y') }}
                                </div>
                                <div class="text-[11px]">
                                    @if($diffDays < 0)
                                        <span class="text-red-500 font-bold">Past Due ({{ abs($diffDays) }}d ago)</span>
                                    @elseif($diffDays === 0)
                                        <span class="text-amber-500 font-bold">Due Today!</span>
                                    @else
                                        <span class="text-gray-400">in {{ $diffDays }} day(s)</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Auto-Post Switch -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                <button
                                    type="button"
                                    wire:click="toggleAutoPost({{ $item->id }})"
                                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $item->auto_post ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                                >
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition duration-200 ease-in-out {{ $item->auto_post ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </td>

                            <!-- Amount -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-right font-black">
                                <span class="{{ $item->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $item->type === 'income' ? '+' : '-' }}{{ auth()->user()->formatMoney($item->amount) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap text-right space-x-1">
                                <button
                                    type="button"
                                    wire:click="postNow({{ $item->id }})"
                                    wire:confirm="Record this transaction right now and roll forward the next due date?"
                                    class="px-2 py-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 rounded-lg transition"
                                    title="Post Immediately"
                                >
                                    Post Now
                                </button>
                                <button
                                    type="button"
                                    wire:click="openEditModal({{ $item->id }})"
                                    class="p-1 text-gray-400 hover:text-indigo-600 rounded"
                                    title="Edit"
                                >
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteRecurring({{ $item->id }})"
                                    wire:confirm="Delete this recurring schedule?"
                                    class="p-1 text-gray-400 hover:text-red-600 rounded"
                                    title="Delete"
                                >
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm text-gray-400">
                                No recurring schedules set up yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Create / Edit Recurring Schedule -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">
                            {{ $editingId ? 'Edit Recurring Item' : 'New Recurring Schedule' }}
                        </h3>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="plus" class="w-5 h-5 rotate-45" />
                        </button>
                    </div>

                    <form wire:submit="save" class="space-y-4">
                        <!-- Type selector -->
                        <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 dark:bg-gray-700/50 rounded-xl">
                            <button
                                type="button"
                                wire:click="$set('type', 'expense')"
                                class="py-1.5 text-xs font-semibold rounded-lg transition {{ $type === 'expense' ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300' }}"
                            >
                                Expense (Bill/Subscription)
                            </button>
                            <button
                                type="button"
                                wire:click="$set('type', 'income')"
                                class="py-1.5 text-xs font-semibold rounded-lg transition {{ $type === 'income' ? 'bg-emerald-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300' }}"
                            >
                                Income (Salary/Retainer)
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="e.g. Netflix, Gym Membership, Monthly Salary"
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-white dark:bg-gray-700/60"
                            />
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="amount"
                                    placeholder="0.00"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-sm font-bold text-gray-900 dark:text-white dark:bg-gray-700/60"
                                />
                                @error('amount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Frequency</label>
                                <select
                                    wire:model="frequency"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                                >
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="biweekly">Bi-Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Wallet</label>
                                <select
                                    wire:model="wallet_id"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                                >
                                    @foreach($wallets as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <select
                                    wire:model="category_id"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                                >
                                    @foreach($categories->where('type', $type) as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                <input
                                    type="date"
                                    wire:model="start_date"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Next Due Date</label>
                                <input
                                    type="date"
                                    wire:model="next_due_date"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                                />
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input
                                type="checkbox"
                                id="auto_post"
                                wire:model="auto_post"
                                class="rounded text-indigo-600 focus:ring-indigo-500"
                            />
                            <label for="auto_post" class="text-xs text-gray-700 dark:text-gray-300">
                                Auto-post transaction when due date arrives
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <input
                                type="text"
                                wire:model="notes"
                                placeholder="Account reference or notes..."
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                            />
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100 dark:border-gray-700">
                            <button
                                type="button"
                                wire:click="$set('showModal', false)"
                                class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 rounded-lg"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm"
                            >
                                Save Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
