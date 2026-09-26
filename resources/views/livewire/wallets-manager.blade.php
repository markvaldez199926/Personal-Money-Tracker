<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Wallets & Accounts
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Manage bank accounts, credit cards, physical cash, and digital wallets
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button
                wire:click="openTransferModal"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl shadow-sm transition"
            >
                <x-icon name="arrow-path" class="w-4 h-4" />
                <span>Transfer Money</span>
            </button>
            <button
                wire:click="openNewWalletModal"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition hover:shadow-indigo-500/20"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>New Wallet</span>
            </button>
        </div>
    </div>

    <!-- Total Balance Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-gray-900 to-indigo-950 p-6 text-white shadow-md border border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-xs uppercase tracking-wider text-indigo-300 font-semibold">Total Liquid Net Worth</span>
            <div class="text-3xl sm:text-4xl font-black mt-1">
                {{ auth()->user()->formatMoney($totalBalance) }}
            </div>
        </div>
        <div class="text-xs text-indigo-200 bg-white/10 px-3.5 py-2 rounded-xl backdrop-blur-sm self-start sm:self-auto">
            <span>{{ $wallets->where('is_active', true)->count() }} Active Accounts</span>
        </div>
    </div>

    <!-- Wallets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($wallets as $wallet)
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-100 dark:border-gray-700/60 relative overflow-hidden transition hover:shadow-md {{ ! $wallet->is_active ? 'opacity-60' : '' }}">
                <!-- Color bar accent -->
                <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: {{ $wallet->color_hex }}"></div>

                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-sm" style="background-color: {{ $wallet->color_hex }}">
                            <x-icon :name="$wallet->icon" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gray-900 dark:text-white">{{ $wallet->name }}</h3>
                            <span class="inline-block text-xs text-gray-500 dark:text-gray-400">{{ $wallet->type_label }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <button
                            wire:click="openEditWalletModal({{ $wallet->id }})"
                            class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                            title="Edit Wallet"
                        >
                            <x-icon name="pencil" class="w-4 h-4" />
                        </button>
                        <button
                            wire:click="deleteWallet({{ $wallet->id }})"
                            wire:confirm="Are you sure? If this wallet has transactions, it will be archived safely."
                            class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                            title="Archive / Delete"
                        >
                            <x-icon name="trash" class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div class="mt-6">
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Available Balance</span>
                    <div class="text-2xl font-black text-gray-900 dark:text-white mt-0.5">
                        {{ auth()->user()->formatMoney($wallet->balance) }}
                    </div>
                </div>

                @if($wallet->notes)
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/60 text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ $wallet->notes }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- New / Edit Wallet Modal -->
    @if($showWalletModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" wire:click="$set('showWalletModal', false)"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">
                            {{ $editingWalletId ? 'Edit Wallet' : 'Create New Wallet' }}
                        </h3>
                        <button wire:click="$set('showWalletModal', false)" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="plus" class="w-5 h-5 rotate-45" />
                        </button>
                    </div>

                    <form wire:submit="saveWallet" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Wallet Name</label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="e.g. Bank of America, Cash, Apple Card"
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Account Type</label>
                                <select
                                    wire:model="type"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank Account</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="digital_wallet">E-Wallet</option>
                                    <option value="savings">Savings</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Current Balance</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="balance"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                @error('balance') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Color & Icon -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="color"
                                        wire:model="color_hex"
                                        class="h-9 w-12 rounded-lg border-0 cursor-pointer bg-transparent"
                                    />
                                    <input
                                        type="text"
                                        wire:model="color_hex"
                                        class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Icon</label>
                                <select
                                    wire:model="icon"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="wallet">Wallet</option>
                                    <option value="credit-card">Credit Card</option>
                                    <option value="banknotes">Cash / Notes</option>
                                    <option value="building-library">Bank Building</option>
                                    <option value="device-phone-mobile">Smartphone / App</option>
                                    <option value="arrow-trending-up">Savings / Investment</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                            <input
                                type="text"
                                wire:model="notes"
                                placeholder="Account number, branch, or notes..."
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                            />
                        </div>

                        @if($editingWalletId)
                            <div class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    id="is_active"
                                    wire:model="is_active"
                                    class="rounded text-indigo-600 focus:ring-indigo-500"
                                />
                                <label for="is_active" class="text-xs text-gray-700 dark:text-gray-300">Account is Active</label>
                            </div>
                        @endif

                        <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100 dark:border-gray-700">
                            <button
                                type="button"
                                wire:click="$set('showWalletModal', false)"
                                class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 rounded-lg"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm"
                            >
                                Save Wallet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Transfer Modal -->
    @if($showTransferModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" wire:click="$set('showTransferModal', false)"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">Transfer Funds</h3>
                        <button wire:click="$set('showTransferModal', false)" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="plus" class="w-5 h-5 rotate-45" />
                        </button>
                    </div>

                    <form wire:submit="executeTransfer" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">From Wallet</label>
                            <select
                                wire:model="from_wallet_id"
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                            >
                                @foreach($wallets->where('is_active', true) as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }} ({{ auth()->user()->formatMoney($w->balance) }})</option>
                                @endforeach
                            </select>
                            @error('from_wallet_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">To Wallet</label>
                            <select
                                wire:model="to_wallet_id"
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                            >
                                @foreach($wallets->where('is_active', true) as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }} ({{ auth()->user()->formatMoney($w->balance) }})</option>
                                @endforeach
                            </select>
                            @error('to_wallet_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Transfer Amount</label>
                            <input
                                type="number"
                                step="0.01"
                                wire:model="transfer_amount"
                                placeholder="0.00"
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-sm font-bold text-gray-900 dark:text-white dark:bg-gray-700/60"
                            />
                            @error('transfer_amount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <input
                                type="text"
                                wire:model="transfer_notes"
                                placeholder="e.g. ATM withdrawal, savings contribution"
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white dark:bg-gray-700/60"
                            />
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100 dark:border-gray-700">
                            <button
                                type="button"
                                wire:click="$set('showTransferModal', false)"
                                class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 rounded-lg"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm"
                            >
                                Complete Transfer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
