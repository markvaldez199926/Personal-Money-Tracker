<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $currency = 'PHP';

    public function mount(): void
    {
        $this->currency = Auth::user()->currency ?? 'PHP';
    }

    public function updateCurrency(): void
    {
        $user = Auth::user();
        if ($user->setCurrency($this->currency)) {
            $this->dispatch('currency-changed', currency: $user->currency, symbol: $user->currency_symbol);
            $this->dispatch('profile-updated', name: $user->name);
            session()->flash('status', 'currency-updated');
        }
    }
}; ?>

<section>
    <header>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base">
                {{ auth()->user()->currency_symbol ?? '₱' }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Currency & Display Format') }}
                </h2>
                <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-400">
                    {{ __('Select your primary currency. All balances, transactions, budgets, and analytics will instantly format using this symbol.') }}
                </p>
            </div>
        </div>
    </header>

    <form wire:submit="updateCurrency" class="mt-6 space-y-6">
        <!-- Currency Selection Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach(User::SUPPORTED_CURRENCIES as $code => $info)
                <label
                    wire:key="curr-{{ $code }}"
                    class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all {{ $currency === $code ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30 dark:border-indigo-500 shadow-sm' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/60 hover:border-gray-300 dark:hover:border-gray-600' }}"
                >
                    <input
                        type="radio"
                        wire:model.live="currency"
                        value="{{ $code }}"
                        class="sr-only"
                    />
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl">{{ $info['flag'] }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-black {{ $currency === $code ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            {{ $info['symbol'] }} {{ $code }}
                        </span>
                    </div>
                    <div class="mt-auto">
                        <div class="font-bold text-sm text-gray-900 dark:text-white">
                            {{ $info['name'] }}
                        </div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                            Sample: {{ $info['symbol'] }}1,250.00
                        </div>
                    </div>
                    @if($currency === $code)
                        <div class="absolute top-2 right-2 w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400"></div>
                    @endif
                </label>
            @endforeach
        </div>

        <!-- Live Preview Banner -->
        @php
            $selected = User::SUPPORTED_CURRENCIES[$currency] ?? User::SUPPORTED_CURRENCIES['PHP'];
        @endphp
        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div>
                <span class="font-semibold text-gray-700 dark:text-gray-300">Live Format Preview:</span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">Example wallet and transaction display</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-400 font-bold">
                    +{{ $selected['symbol'] }}25,000.00
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-400 font-bold">
                    -{{ $selected['symbol'] }}1,850.50
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-400 font-bold">
                    {{ $selected['symbol'] }}142,320.00
                </span>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                {{ __('Save Currency Preference') }}
            </x-primary-button>

            <x-action-message class="me-3 text-emerald-600 dark:text-emerald-400 font-medium text-xs" on="currency-updated">
                {{ __('Saved. Your currency has been updated!') }}
            </x-action-message>
        </div>
    </form>
</section>
