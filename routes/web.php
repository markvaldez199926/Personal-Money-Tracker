<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('transactions', 'transactions')->name('transactions');
    Route::view('wallets', 'wallets')->name('wallets');
    Route::view('categories', 'categories')->name('categories');
    Route::view('budgets', 'budgets')->name('budgets');
    Route::view('recurring', 'recurring')->name('recurring');
    Route::view('analytics', 'analytics')->name('analytics');
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
