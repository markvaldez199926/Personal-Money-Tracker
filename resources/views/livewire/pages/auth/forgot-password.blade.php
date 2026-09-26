<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50 dark:bg-gray-950">
    <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-8 space-y-6">
        <!-- Brand Logo -->
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-md">
                <x-icon name="wallet" class="w-5 h-5" />
            </div>
            <span class="text-xl font-black text-gray-900 dark:text-white">
                Money<span class="text-indigo-600 dark:text-indigo-400">Tracker</span>
            </span>
        </div>

        <div>
            <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white">
                Reset your password
            </h2>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                Enter the email address associated with your account, and we'll send you a secure link to reset your password.
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="sendPasswordResetLink" class="space-y-4">
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
                        autofocus
                        placeholder="you@example.com"
                        class="block w-full rounded-xl border-gray-200 dark:border-gray-700 pl-10 pr-4 py-2.5 text-sm text-gray-900 dark:text-white dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/25 transition active:scale-[0.99] disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="sendPasswordResetLink">Send Reset Link &rarr;</span>
                    <span wire:loading wire:target="sendPasswordResetLink">Sending email...</span>
                </button>
            </div>
        </form>

        <div class="pt-4 border-t border-gray-100 dark:border-gray-800 text-center">
            <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                <span>&larr;</span>
                <span>Return to sign in</span>
            </a>
        </div>
    </div>
</div>
