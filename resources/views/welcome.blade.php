<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MoneyTracker - Master Your Daily Finances</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col justify-between">
        <!-- Top Nav -->
        <header class="max-w-7xl mx-auto px-6 py-6 w-full flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/25">
                    <x-icon name="wallet" class="w-5 h-5" />
                </div>
                <span class="text-xl font-black tracking-tight text-gray-900 dark:text-white">
                    Money<span class="text-indigo-600 dark:text-indigo-400">Tracker</span>
                </span>
            </div>

            <nav class="flex items-center gap-3">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition"
                    >
                        Go to Dashboard &rarr;
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-indigo-600 transition"
                    >
                        Log In
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition"
                    >
                        Sign Up
                    </a>
                @endauth
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="max-w-6xl mx-auto px-6 py-12 flex-1 flex flex-col items-center text-center justify-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 text-xs font-bold mb-6">
                <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                <span>Personal Money Tracker & Budget Engine</span>
            </div>

            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-gray-900 dark:text-white max-w-3xl leading-tight sm:leading-none">
                Track every penny. <br/>
                <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 bg-clip-text text-transparent">
                    Build lasting wealth.
                </span>
            </h1>

            <p class="mt-6 text-base sm:text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
                Take control of your daily expenses, multi-wallet cash flow, monthly category budgets, and recurring subscriptions with live real-time insights.
            </p>

            <!-- Main CTA Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row items-center gap-3">
                <a
                    href="{{ route('register') }}"
                    class="py-3.5 px-8 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/25 transition active:scale-[0.99]"
                >
                    Get Started Free &rarr;
                </a>
                <a
                    href="{{ route('login') }}"
                    class="py-3.5 px-8 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 shadow-sm transition"
                >
                    Sign In to Account
                </a>
            </div>

            <!-- 4 Value Props Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-16 text-left w-full">
                <!-- Card 1 -->
                <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 flex items-center justify-center mb-4">
                        <x-icon name="wallet" class="w-5 h-5" />
                    </div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Multi-Wallet Hub</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Track separate balances for Cash, Bank Accounts, Credit Cards, and E-Wallets with seamless transfers.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center mb-4">
                        <x-icon name="arrow-trending-up" class="w-5 h-5" />
                    </div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Category Budgets</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Set custom monthly spending limits per category with live color-coded progress bars and 80% threshold warnings.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 flex items-center justify-center mb-4">
                        <x-icon name="calendar" class="w-5 h-5" />
                    </div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Recurring Schedules</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Automate subscriptions, rent, and salary paydays. Auto-post or review due items with countdown alerts.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 flex items-center justify-center mb-4">
                        <x-icon name="trending-up" class="w-5 h-5" />
                    </div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Visual Analytics</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Interactive cash flow charts, category breakdown donut graphs, top merchant rankings, and CSV ledger exports.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-100 dark:border-gray-900 bg-white dark:bg-gray-950/80 w-full mt-20">
            <div class="max-w-7xl mx-auto px-6 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Brand info -->
                    <div class="md:col-span-2 space-y-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-md">
                                <x-icon name="wallet" class="w-5 h-5" />
                            </div>
                            <span class="text-lg font-black tracking-tight text-gray-900 dark:text-white">
                                Money<span class="text-indigo-600 dark:text-indigo-400">Tracker</span>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm leading-relaxed">
                            A secure, private personal money tracker built with modern architecture to give you full visibility over daily expenses, multi-wallet accounts, budgets, and cash flow.
                        </p>
                        <div class="flex items-center gap-2 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Bank-grade privacy &bull; Self-hosted & personal</span>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Product</h4>
                        <ul class="space-y-2 text-xs text-gray-500 dark:text-gray-400">
                            <li><a href="{{ route('dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Financial Dashboard</a></li>
                            <li><a href="{{ route('transactions') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Transactions Ledger</a></li>
                            <li><a href="{{ route('wallets') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Wallets & Accounts</a></li>
                            <li><a href="{{ route('budgets') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Monthly Budgets</a></li>
                            <li><a href="{{ route('analytics') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Analytics & Reports</a></li>
                        </ul>
                    </div>

                    <!-- Quick Access -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Account</h4>
                        <ul class="space-y-2 text-xs text-gray-500 dark:text-gray-400">
                            @auth
                                <li><a href="{{ route('dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-semibold transition">Open Dashboard &rarr;</a></li>
                                <li><a href="{{ route('profile') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Profile & Settings</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Sign In</a></li>
                                <li><a href="{{ route('register') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-semibold transition">Create Free Account</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="pt-8 mt-8 border-t border-gray-100 dark:border-gray-900 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-400 gap-4">
                    <div>
                        &copy; {{ date('Y') }} MoneyTracker. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
