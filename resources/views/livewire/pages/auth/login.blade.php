<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex flex-col lg:flex-row">
    <!-- Left Showcase Column (Desktop) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-gray-900 via-indigo-950 to-slate-950 p-12 flex-col justify-between text-white border-r border-gray-800">
        <!-- Ambient glowing orbs background -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Top Brand -->
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-xl shadow-indigo-500/25">
                <x-icon name="wallet" class="w-6 h-6" />
            </div>
            <span class="text-2xl font-black tracking-tight text-white">
                Money<span class="text-indigo-400">Tracker</span>
            </span>
        </div>

        <!-- Middle Hero Pitch & Glassmorphic Preview Widget -->
        <div class="relative z-10 my-auto py-8 max-w-lg">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-semibold mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Smart Personal Finance</span>
            </div>

            <h1 class="text-4xl lg:text-5xl font-black tracking-tight leading-tight text-white mb-4">
                Take command of your financial journey.
            </h1>
            <p class="text-base text-gray-300 leading-relaxed mb-8">
                Track daily expenses across multiple wallets, manage monthly budgets with live threshold alerts, and visualize cash flow in one seamless place.
            </p>

            <!-- Glassmorphic Preview Card -->
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-indigo-200">Net Portfolio Worth</span>
                        <div class="text-2xl font-black text-white mt-0.5">$18,760.75</div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        +14.2% this month
                    </span>
                </div>

                <!-- Mini Wallet Chips -->
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-white/5 border border-white/5">
                        <div class="text-[10px] text-gray-400">Checking</div>
                        <div class="font-bold text-white mt-0.5">$4,820</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/5 border border-white/5">
                        <div class="text-[10px] text-gray-400">Savings</div>
                        <div class="font-bold text-white mt-0.5">$12,500</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/5 border border-white/5">
                        <div class="text-[10px] text-gray-400">E-Wallet</div>
                        <div class="font-bold text-white mt-0.5">$1,440</div>
                    </div>
                </div>

                <!-- Mini Budget Meter -->
                <div class="pt-2 border-t border-white/10">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-300 font-medium">Groceries Budget</span>
                        <span class="text-emerald-400 font-bold">$142.30 / $600.00</span>
                    </div>
                    <div class="w-full bg-white/10 h-2 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-400 rounded-full" style="width: 24%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Trust Badge -->
        <div class="relative z-10 flex items-center justify-between text-xs text-gray-400 pt-6 border-t border-white/10">
            <span>&copy; {{ date('Y') }} MoneyTracker Inc.</span>
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Secure & Encrypted Sessions
            </span>
        </div>
    </div>

    <!-- Right Form Column -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 lg:p-16 bg-white dark:bg-gray-900">
        <div class="w-full max-w-md space-y-6">
            <!-- Mobile Brand Logo -->
            <div class="lg:hidden flex items-center gap-2.5 mb-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-md">
                    <x-icon name="wallet" class="w-5 h-5" />
                </div>
                <span class="text-xl font-black text-gray-900 dark:text-white">
                    Money<span class="text-indigo-600 dark:text-indigo-400">Tracker</span>
                </span>
            </div>

            <!-- Header -->
            <div>
                <h2 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">
                    Welcome back
                </h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Sign in to access your personal dashboard and transactions.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Form -->
            <form wire:submit="login" class="space-y-4">
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Email Address
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input
                            wire:model="form.email"
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@example.com"
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 pl-10 pr-4 py-2.5 text-sm text-gray-900 dark:text-white dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
                </div>

                <!-- Password with Show/Hide Toggle -->
                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                wire:navigate
                                class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline"
                            >
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            wire:model="form.password"
                            id="password"
                            :type="show ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 pl-10 pr-10 py-2.5 text-sm text-gray-900 dark:text-white dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
                            title="Toggle password visibility"
                        >
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" class="w-4 h-4" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label for="remember" class="inline-flex items-center cursor-pointer">
                        <input
                            wire:model="form.remember"
                            id="remember"
                            type="checkbox"
                            class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-800"
                            name="remember"
                        />
                        <span class="ms-2 text-xs text-gray-600 dark:text-gray-400">Keep me signed in for 30 days</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/25 transition active:scale-[0.99] disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="login">Sign In to Dashboard &rarr;</span>
                        <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Authenticating...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Bottom Switch to Register & Back Home -->
            <div class="pt-4 border-t border-gray-100 dark:border-gray-800 text-center space-y-3">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Don't have an account yet?
                    <a href="{{ route('register') }}" wire:navigate class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Create an account
                    </a>
                </p>

                <div>
                    <a href="{{ url('/') }}" wire:navigate class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <span>&larr;</span>
                        <span>Back to overview</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
