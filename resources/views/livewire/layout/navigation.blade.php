<?php

use App\Livewire\Actions\Logout;
use App\Models\User;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Switch the user's currency.
     */
    public function switchCurrency(string $code): void
    {
        $user = auth()->user();
        if ($user && $user->setCurrency($code)) {
            $this->dispatch('currency-changed', currency: $code, symbol: $user->currency_symbol);
            $this->redirect(request()->header('Referer') ?: route('dashboard'), navigate: true);
        }
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700/60 sticky top-0 z-40 backdrop-blur-md bg-white/95 dark:bg-gray-800/95">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                            <x-icon name="wallet" class="w-5 h-5" />
                        </div>
                        <span class="text-lg font-black tracking-tight text-gray-900 dark:text-white">
                            Money<span class="text-indigo-600 dark:text-indigo-400">Tracker</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:-my-px md:ms-8 md:flex md:space-x-1 lg:space-x-2">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate class="text-xs font-semibold px-3">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('transactions')" :active="request()->routeIs('transactions')" wire:navigate class="text-xs font-semibold px-3">
                        {{ __('Transactions') }}
                    </x-nav-link>
                    <x-nav-link :href="route('wallets')" :active="request()->routeIs('wallets')" wire:navigate class="text-xs font-semibold px-3">
                        {{ __('Wallets') }}
                    </x-nav-link>
                    <x-nav-link :href="route('categories')" :active="request()->routeIs('categories')" wire:navigate class="text-xs font-semibold px-3">
                        {{ __('Categories') }}
                    </x-nav-link>
                    <x-nav-link :href="route('budgets')" :active="request()->routeIs('budgets')" wire:navigate class="text-xs font-semibold px-3">
                        {{ __('Budgets') }}
                    </x-nav-link>
                    <x-nav-link :href="route('recurring')" :active="request()->routeIs('recurring')" wire:navigate class="text-xs font-semibold px-3">
                        {{ __('Recurring') }}
                    </x-nav-link>
                    <x-nav-link :href="route('analytics')" :active="request()->routeIs('analytics')" wire:navigate class="text-xs font-semibold px-3">
                        {{ __('Analytics') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right side items -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2.5">
                <!-- Currency Quick Selector Dropdown -->
                @php
                    $userCurrency = auth()->user()->currency ?? 'PHP';
                    $currInfo = User::SUPPORTED_CURRENCIES[$userCurrency] ?? User::SUPPORTED_CURRENCIES['PHP'];
                @endphp
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 border border-gray-200 dark:border-gray-700 text-xs font-bold rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm"
                            title="Click to Switch Currency"
                        >
                            <span>{{ $currInfo['flag'] }}</span>
                            <span class="text-indigo-600 dark:text-indigo-400 font-black">{{ $currInfo['symbol'] }}</span>
                            <span class="font-semibold">{{ $userCurrency }}</span>
                            <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                            Select Currency
                        </div>
                        @foreach(User::SUPPORTED_CURRENCIES as $cCode => $info)
                            <button
                                type="button"
                                wire:click="switchCurrency('{{ $cCode }}')"
                                class="w-full text-left flex items-center justify-between px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 transition {{ $userCurrency === $cCode ? 'font-bold bg-indigo-50/50 dark:bg-gray-700/60 text-indigo-600 dark:text-indigo-400' : '' }}"
                            >
                                <span class="flex items-center gap-2">
                                    <span>{{ $info['flag'] }}</span>
                                    <span>{{ $info['name'] }}</span>
                                </span>
                                <span class="font-mono text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-750 text-gray-600 dark:text-gray-300">
                                    {{ $info['symbol'] }} {{ $cCode }}
                                </span>
                            </button>
                        @endforeach
                    </x-slot>
                </x-dropdown>

                <!-- Quick Add Trigger Button -->
                <button
                    type="button"
                    x-on:click="$dispatch('open-quick-add')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow-indigo-500/25 transition"
                    title="Press Ctrl+K to open"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Quick Add</span>
                    <kbd class="hidden lg:inline-block ml-1 px-1.5 py-0.5 text-[10px] font-mono bg-white/20 rounded">Ctrl+K</kbd>
                </button>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-200 dark:border-gray-700 text-xs font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1.5">
                                <svg class="fill-current h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 text-xs text-gray-400">
                            Signed in as <span class="font-bold text-gray-800 dark:text-gray-200">{{ auth()->user()->email }}</span>
                        </div>

                        <x-dropdown-link :href="route('profile')" wire:navigate class="text-xs">
                            {{ __('Profile & Settings') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start text-xs">
                            <x-dropdown-link class="text-red-600 dark:text-red-400">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger for Mobile -->
            <div class="-me-2 flex items-center sm:hidden gap-2">
                <button
                    type="button"
                    x-on:click="$dispatch('open-quick-add')"
                    class="p-2 bg-indigo-600 text-white rounded-lg shadow-sm"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                </button>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-100 dark:border-gray-700/60">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('transactions')" :active="request()->routeIs('transactions')" wire:navigate>
                {{ __('Transactions') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('wallets')" :active="request()->routeIs('wallets')" wire:navigate>
                {{ __('Wallets') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories')" :active="request()->routeIs('categories')" wire:navigate>
                {{ __('Categories') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('budgets')" :active="request()->routeIs('budgets')" wire:navigate>
                {{ __('Budgets') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('recurring')" :active="request()->routeIs('recurring')" wire:navigate>
                {{ __('Recurring') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('analytics')" :active="request()->routeIs('analytics')" wire:navigate>
                {{ __('Analytics') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Currency Selector -->
        <div class="py-3 border-t border-gray-100 dark:border-gray-700/60 px-4">
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Switch Currency:</div>
            <div class="grid grid-cols-2 gap-2">
                @foreach(['PHP', 'USD', 'EUR', 'GBP'] as $mCurr)
                    @php $mInfo = User::SUPPORTED_CURRENCIES[$mCurr]; @endphp
                    <button
                        type="button"
                        wire:click="switchCurrency('{{ $mCurr }}')"
                        class="px-2.5 py-1.5 rounded-xl border text-xs font-bold flex items-center justify-between transition {{ ($userCurrency ?? 'PHP') === $mCurr ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300' }}"
                    >
                        <span>{{ $mInfo['flag'] }} {{ $mCurr }}</span>
                        <span>{{ $mInfo['symbol'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-600 px-4">
            <div class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ auth()->user()->name }}</div>
            <div class="font-medium text-xs text-gray-500">{{ auth()->user()->email }}</div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link class="text-red-500">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
