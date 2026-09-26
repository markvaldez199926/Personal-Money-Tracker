<?php

use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $currency = 'USD';
    public string $currency_symbol = '$';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'currency' => $this->currency,
            'currency_symbol' => $this->currency_symbol,
        ]);

        // Create default starter wallet for the new user
        Wallet::create([
            'user_id' => $user->id,
            'name' => 'Main Checking',
            'type' => 'bank',
            'balance' => 0.00,
            'color_hex' => '#2563eb',
            'icon' => 'building-library',
            'is_active' => true,
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'name' => 'Cash',
            'type' => 'cash',
            'balance' => 0.00,
            'color_hex' => '#10b981',
            'icon' => 'banknotes',
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex flex-col lg:flex-row">
    <!-- Left Showcase Column (Desktop) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-gray-900 via-indigo-950 to-slate-950 p-12 flex-col justify-between text-white border-r border-gray-800">
        <!-- Ambient glowing orbs background -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Top Brand -->
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-xl shadow-indigo-500/25">
                <x-icon name="wallet" class="w-6 h-6" />
            </div>
            <span class="text-2xl font-black tracking-tight text-white">
                Money<span class="text-indigo-400">Tracker</span>
            </span>
        </div>

        <!-- Middle Hero Pitch & Steps -->
        <div class="relative z-10 my-auto py-8 max-w-lg">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-semibold mb-6">
                <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                <span>Get Started in 30 Seconds</span>
            </div>

            <h1 class="text-4xl lg:text-5xl font-black tracking-tight leading-tight text-white mb-4">
                Start building your financial freedom today.
            </h1>
            <p class="text-base text-gray-300 leading-relaxed mb-8">
                Join thousands of users organizing their daily spending, meeting monthly savings goals, and taking charge of their cash flow.
            </p>

            <!-- Feature Checkmarks -->
            <div class="space-y-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-200">Unlimited multi-wallet accounts (Cash, Bank, Cards)</span>
                </div>
                <div class="flex items-center gap-3.5">
                    <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-200">Monthly category budgets with 80% threshold warnings</span>
                </div>
                <div class="flex items-center gap-3.5">
                    <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-200">Receipt image attachments and CSV ledger exports</span>
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
                100% Free & Private Data
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
                    Create account
                </h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Sign up to start tracking your daily expenses and budgets.
                </p>
            </div>

            <!-- Registration Form -->
            <form wire:submit="register" class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Full Name
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input
                            wire:model="name"
                            id="name"
                            type="text"
                            name="name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Alex Morgan"
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 pl-10 pr-4 py-2.5 text-sm text-gray-900 dark:text-white dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                </div>

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
                            wire:model="email"
                            id="email"
                            type="email"
                            name="email"
                            required
                            autocomplete="username"
                            placeholder="you@example.com"
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 pl-10 pr-4 py-2.5 text-sm text-gray-900 dark:text-white dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Password
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            wire:model="password"
                            id="password"
                            :type="show ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 pl-10 pr-10 py-2.5 text-sm text-gray-900 dark:text-white dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
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
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Confirm Password
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <input
                            wire:model="password_confirmation"
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 pl-10 pr-4 py-2.5 text-sm text-gray-900 dark:text-white dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/25 transition active:scale-[0.99] disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="register">Create Free Account &rarr;</span>
                        <span wire:loading wire:target="register" class="inline-flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating Account...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Bottom Switch to Login -->
            <div class="pt-4 border-t border-gray-100 dark:border-gray-800 text-center space-y-3">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Already have an account?
                    <a href="{{ route('login') }}" wire:navigate class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Sign In
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
