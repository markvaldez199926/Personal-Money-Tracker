<?php

use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use App\Services\TransactionService;

beforeEach(function () {
    $this->user = User::factory()->create([
        'currency' => 'USD',
        'currency_symbol' => '$',
    ]);

    $this->wallet = Wallet::create([
        'user_id' => $this->user->id,
        'name' => 'Main Checking',
        'type' => 'bank',
        'balance' => 1000.00,
        'color_hex' => '#2563eb',
        'icon' => 'building-library',
        'is_active' => true,
    ]);

    $this->category = Category::create([
        'user_id' => $this->user->id,
        'name' => 'Groceries',
        'type' => 'expense',
        'icon' => 'shopping-cart',
        'color_hex' => '#10b981',
        'is_default' => false,
    ]);
});

test('dashboard can be rendered for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee('Financial Overview');
});

test('transactions page can be rendered for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('transactions'));
    $response->assertOk();
    $response->assertSee('Transactions Ledger');
});

test('wallets page can be rendered for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('wallets'));
    $response->assertOk();
    $response->assertSee('Wallets & Accounts', false);
    $response->assertSee('Main Checking');
});

test('budgets page can be rendered for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('budgets'));
    $response->assertOk();
    $response->assertSee('Monthly Budgets');
});

test('recurring page can be rendered for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('recurring'));
    $response->assertOk();
    $response->assertSee('Recurring Bills & Incomes', false);
});

test('analytics page can be rendered for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('analytics'));
    $response->assertOk();
    $response->assertSee('Financial Analytics & Insights', false);
});

test('transaction service creates expense and decrements wallet balance', function () {
    $service = app(TransactionService::class);

    $tx = $service->create($this->user, [
        'wallet_id' => $this->wallet->id,
        'category_id' => $this->category->id,
        'type' => 'expense',
        'amount' => 150.00,
        'transaction_date' => now()->toDateString(),
        'payee_merchant' => 'Supermarket',
        'notes' => 'Weekly food items',
        'tags' => ['food', 'groceries'],
    ]);

    expect($tx)->not->toBeNull();
    expect($this->wallet->fresh()->balance)->toEqual(850.00);
});

test('transaction service creates transfer between two wallets atomically', function () {
    $targetWallet = Wallet::create([
        'user_id' => $this->user->id,
        'name' => 'Cash',
        'type' => 'cash',
        'balance' => 50.00,
        'color_hex' => '#10b981',
        'icon' => 'banknotes',
    ]);

    $service = app(TransactionService::class);

    $tx = $service->create($this->user, [
        'wallet_id' => $this->wallet->id,
        'to_wallet_id' => $targetWallet->id,
        'type' => 'transfer',
        'amount' => 100.00,
        'transaction_date' => now()->toDateString(),
        'notes' => 'ATM withdrawal',
    ]);

    expect($this->wallet->fresh()->balance)->toEqual(900.00);
    expect($targetWallet->fresh()->balance)->toEqual(150.00);
});
