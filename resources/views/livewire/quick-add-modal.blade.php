<div>
    @if($isOpen)
    <div
        x-data
        x-on:keydown.escape.window="$wire.closeModal()"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true"
    >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-gray-700">
                <!-- Header -->
                <div class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="modal-title">
                            Add Transaction
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Record your income, expense, or wallet transfer</p>
                    </div>
                    <button
                        wire:click="closeModal"
                        type="button"
                        class="rounded-lg p-1.5 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                    >
                        <x-icon name="plus" class="w-5 h-5 rotate-45" />
                    </button>
                </div>

                <!-- Form Content -->
                <form wire:submit="save">
                    <div class="px-6 py-5 space-y-4">
                        <!-- Transaction Type Selector -->
                        <div class="grid grid-cols-3 gap-2 p-1 bg-gray-100 dark:bg-gray-700/50 rounded-xl">
                            <button
                                type="button"
                                wire:click="$set('type', 'expense')"
                                class="py-2 text-xs font-semibold rounded-lg transition-all {{ $type === 'expense' ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}"
                            >
                                Expense
                            </button>
                            <button
                                type="button"
                                wire:click="$set('type', 'income')"
                                class="py-2 text-xs font-semibold rounded-lg transition-all {{ $type === 'income' ? 'bg-emerald-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}"
                            >
                                Income
                            </button>
                            <button
                                type="button"
                                wire:click="$set('type', 'transfer')"
                                class="py-2 text-xs font-semibold rounded-lg transition-all {{ $type === 'transfer' ? 'bg-indigo-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}"
                            >
                                Transfer
                            </button>
                        </div>

                        <!-- Amount Field -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <span class="text-xl font-bold text-gray-500 dark:text-gray-400">{{ auth()->user()->currency_symbol ?? '$' }}</span>
                                </div>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    placeholder="0.00"
                                    wire:model="amount"
                                    autofocus
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 pl-10 pr-4 py-3 text-2xl font-bold text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500 sm:text-2xl"
                                />
                            </div>
                            @error('amount') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Wallets Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $type === 'transfer' ? 'From Wallet' : 'Wallet / Account' }}
                                </label>
                                <select
                                    wire:model="wallet_id"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 py-2 px-3 text-sm text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    @foreach($wallets as $wallet)
                                        <option value="{{ $wallet->id }}">
                                            {{ $wallet->name }} ({{ auth()->user()->formatMoney($wallet->balance) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('wallet_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($type === 'transfer')
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">To Wallet</label>
                                    <select
                                        wire:model="to_wallet_id"
                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 py-2 px-3 text-sm text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Select destination...</option>
                                        @foreach($wallets as $wallet)
                                            @if($wallet->id != $wallet_id)
                                                <option value="{{ $wallet->id }}">
                                                    {{ $wallet->name }} ({{ auth()->user()->formatMoney($wallet->balance) }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('to_wallet_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @else
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                    <select
                                        wire:model="category_id"
                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 py-2 px-3 text-sm text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>

                        <!-- Date & Payee/Merchant -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                <input
                                    type="date"
                                    wire:model="transaction_date"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 py-2 px-3 text-sm text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                @error('transaction_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($type !== 'transfer')
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Payee / Merchant</label>
                                    <input
                                        type="text"
                                        placeholder="e.g. Starbucks, Amazon"
                                        wire:model="payee_merchant"
                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 py-2 px-3 text-sm text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('payee_merchant') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>

                        <!-- Notes & Tags -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tags (comma-separated)</label>
                            <input
                                type="text"
                                placeholder="vacation, tax, client, food"
                                wire:model="tag_input"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 py-2 px-3 text-sm text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea
                                rows="2"
                                placeholder="Optional description..."
                                wire:model="notes"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 py-2 px-3 text-sm text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                            @error('notes') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Receipt Upload -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Attach Receipt (Optional)</label>
                            <input
                                type="file"
                                wire:model="receipt"
                                accept="image/*"
                                class="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100"
                            />
                            <div wire:loading wire:target="receipt" class="text-xs text-indigo-500 mt-1">Uploading receipt preview...</div>
                            @if($receipt)
                                <div class="mt-2 flex items-center gap-2">
                                    <img src="{{ $receipt->temporaryUrl() }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 dark:border-gray-700" alt="Receipt Preview" />
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Receipt attached</span>
                                </div>
                            @endif
                            @error('receipt') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="bg-gray-50 dark:bg-gray-700/30 px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 rounded-xl shadow-md hover:shadow-indigo-500/25 transition disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="save">Save Transaction</span>
                            <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
