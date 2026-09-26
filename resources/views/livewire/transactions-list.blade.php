<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Transactions Ledger
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Filter, search, review receipts, and export your transaction history
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button
                wire:click="exportCsv"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl shadow-sm transition"
            >
                <x-icon name="arrow-path" class="w-4 h-4 rotate-90" />
                <span>Export CSV</span>
            </button>

            <button
                type="button"
                x-on:click="$dispatch('open-quick-add')"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition hover:shadow-indigo-500/20"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Add Transaction</span>
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm border border-gray-100 dark:border-gray-700/60 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search Payee / Notes</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by merchant, note, or tag..."
                    class="block w-full rounded-xl border-gray-200 dark:border-gray-700 py-2 px-3 text-xs text-gray-900 dark:text-white dark:bg-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500"
                />
            </div>

            <!-- Type -->
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Type</label>
                <select
                    wire:model.live="type"
                    class="block w-full rounded-xl border-gray-200 dark:border-gray-700 py-2 px-3 text-xs text-gray-900 dark:text-white dark:bg-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">All Types</option>
                    <option value="expense">Expense</option>
                    <option value="income">Income</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>

            <!-- Wallet -->
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Wallet</label>
                <select
                    wire:model.live="wallet_id"
                    class="block w-full rounded-xl border-gray-200 dark:border-gray-700 py-2 px-3 text-xs text-gray-900 dark:text-white dark:bg-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">All Wallets</option>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Category</label>
                <select
                    wire:model.live="category_id"
                    class="block w-full rounded-xl border-gray-200 dark:border-gray-700 py-2 px-3 text-xs text-gray-900 dark:text-white dark:bg-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Date Range Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-700/60">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mr-2">Period:</span>
            <button
                type="button"
                wire:click="$set('date_range', 'month')"
                class="px-3 py-1 text-xs rounded-lg font-medium transition {{ $date_range === 'month' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}"
            >
                This Month
            </button>
            <button
                type="button"
                wire:click="$set('date_range', 'last_month')"
                class="px-3 py-1 text-xs rounded-lg font-medium transition {{ $date_range === 'last_month' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}"
            >
                Last Month
            </button>
            <button
                type="button"
                wire:click="$set('date_range', 'all')"
                class="px-3 py-1 text-xs rounded-lg font-medium transition {{ $date_range === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}"
            >
                All Time
            </button>
            <button
                type="button"
                wire:click="$set('date_range', 'custom')"
                class="px-3 py-1 text-xs rounded-lg font-medium transition {{ $date_range === 'custom' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}"
            >
                Custom Range
            </button>

            @if($date_range === 'custom')
                <div class="flex items-center gap-2 ml-auto">
                    <input
                        type="date"
                        wire:model.live="start_date"
                        class="rounded-lg border-gray-200 dark:border-gray-700 py-1 px-2 text-xs text-gray-900 dark:text-white dark:bg-gray-700/50"
                    />
                    <span class="text-xs text-gray-400">to</span>
                    <input
                        type="date"
                        wire:model.live="end_date"
                        class="rounded-lg border-gray-200 dark:border-gray-700 py-1 px-2 text-xs text-gray-900 dark:text-white dark:bg-gray-700/50"
                    />
                </div>
            @endif
        </div>
    </div>

    <!-- Table Component -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/75 dark:bg-gray-750 text-gray-400 uppercase text-[11px] font-semibold tracking-wider">
                        <th class="py-3.5 px-4 sm:px-6">Date</th>
                        <th class="py-3.5 px-4">Payee & Notes</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Wallet</th>
                        <th class="py-3.5 px-4 text-right">Amount</th>
                        <th class="py-3.5 px-4 text-center">Receipt</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition">
                            <!-- Date -->
                            <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                {{ $tx->transaction_date->format('M d, Y') }}
                            </td>

                            <!-- Payee / Notes / Tags -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ $tx->payee_merchant ?: ($tx->type === 'transfer' ? 'Transfer' : ($tx->category->name ?? 'Uncategorized')) }}
                                </div>
                                @if($tx->notes)
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $tx->notes }}</div>
                                @endif
                                @if(is_array($tx->tags) && count($tx->tags) > 0)
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($tx->tags as $tag)
                                            <span class="inline-flex items-center text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                #{{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <!-- Category -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($tx->category)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                          style="background-color: {{ $tx->category->color_hex }}15; color: {{ $tx->category->color_hex }}">
                                        <x-icon :name="$tx->category->icon" class="w-3.5 h-3.5" />
                                        <span>{{ $tx->category->name }}</span>
                                    </span>
                                @elseif($tx->type === 'transfer')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                                        Transfer
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">&mdash;</span>
                                @endif
                            </td>

                            <!-- Wallet -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-600 dark:text-gray-300">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $tx->wallet->color_hex ?? '#6366f1' }}"></span>
                                    <span>{{ $tx->wallet->name }}</span>
                                </div>
                                @if($tx->type === 'transfer' && $tx->toWallet)
                                    <div class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                                        <span>&rarr; {{ $tx->toWallet->name }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-right font-bold text-sm">
                                <span class="{{ $tx->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : ($tx->type === 'expense' ? 'text-red-600 dark:text-red-400' : 'text-indigo-600 dark:text-indigo-400') }}">
                                    {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '-' : '') }}{{ auth()->user()->formatMoney($tx->amount) }}
                                </span>
                            </td>

                            <!-- Receipt Preview -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                @if($tx->receipt_path)
                                    <button
                                        type="button"
                                        wire:click="showReceipt('{{ $tx->receipt_url }}')"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition"
                                        title="View Receipt"
                                    >
                                        <x-icon name="receipt" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                    </button>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600 text-xs">&mdash;</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap text-right">
                                <button
                                    type="button"
                                    wire:click="deleteTransaction({{ $tx->id }})"
                                    wire:confirm="Are you sure you want to delete this transaction? This will automatically reverse the wallet balance."
                                    class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition"
                                    title="Delete Transaction"
                                >
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm text-gray-400">
                                No transactions match the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/60">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Receipt Preview Lightbox Modal -->
    @if($previewReceiptUrl)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" wire:click="closeReceiptModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative max-w-2xl bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-2xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-900 dark:text-white text-sm">Receipt Attachment</h3>
                        <button wire:click="closeReceiptModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-icon name="plus" class="w-5 h-5 rotate-45" />
                        </button>
                    </div>
                    <img src="{{ $previewReceiptUrl }}" alt="Receipt Attachment" class="max-h-[70vh] w-auto mx-auto rounded-xl object-contain" />
                </div>
            </div>
        </div>
    @endif
</div>
