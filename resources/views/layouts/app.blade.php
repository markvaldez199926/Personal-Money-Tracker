<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900"
          x-data
          x-on:keydown.window.ctrl.k.prevent="$dispatch('open-quick-add')"
          x-on:keydown.window.meta.k.prevent="$dispatch('open-quick-add')"
    >
        <div class="min-h-screen flex flex-col justify-between bg-gray-50 dark:bg-gray-900">
            <div>
                <livewire:layout.navigation />

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-100 dark:border-gray-700/60">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>

            <!-- Application Footer -->
            <footer class="mt-12 border-t border-gray-100 dark:border-gray-800/80 bg-white/60 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-400 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">MoneyTracker</span>
                        <span>&bull;</span>
                        <span>&copy; {{ date('Y') }} Personal Finance System</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-[11px] text-gray-400">
                            Quick Add: <kbd class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded border border-gray-200 dark:border-gray-600">Ctrl+K</kbd>
                        </span>
                        <span>&bull;</span>
                        <a href="{{ route('profile') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition font-medium">
                            Preferences
                        </a>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Global Quick-Add Modal Accessible Everywhere -->
        <livewire:quick-add-modal />

        <!-- Global Toast Notifications -->
        <x-toast />
    </body>
</html>
