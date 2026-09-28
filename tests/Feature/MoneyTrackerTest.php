<?php

use App\Livewire\CategoriesManager;
use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use App\Services\TransactionService;
use Livewire\Livewire;
use Livewire\Volt\Volt;

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

test('user can switch currency between peso and dollar with correct formatting', function () {
    expect($this->user->formatMoney(1500))->toBe('$1,500.00');

    // Switch to Philippine Peso
    $success = $this->user->setCurrency('PHP');
    expect($success)->toBeTrue();
    expect($this->user->currency)->toBe('PHP');
    expect($this->user->currency_symbol)->toBe('₱');
    expect($this->user->formatMoney(1500))->toBe('₱1,500.00');

    // Switch back to US Dollar
    $success = $this->user->setCurrency('USD');
    expect($success)->toBeTrue();
    expect($this->user->currency)->toBe('USD');
    expect($this->user->currency_symbol)->toBe('$');
    expect($this->user->formatMoney(1500))->toBe('$1,500.00');
});

test('currency update form in profile updates user preference', function () {
    $this->actingAs($this->user);

    Volt::test('profile.update-currency-form')
        ->set('currency', 'PHP')
        ->call('updateCurrency')
        ->assertDispatched('currency-changed');

    expect($this->user->fresh()->currency)->toBe('PHP');
    expect($this->user->fresh()->currency_symbol)->toBe('₱');

    Volt::test('profile.update-currency-form')
        ->set('currency', 'USD')
        ->call('updateCurrency')
        ->assertDispatched('currency-changed');

    expect($this->user->fresh()->currency)->toBe('USD');
    expect($this->user->fresh()->currency_symbol)->toBe('$');
});

test('categories page can be rendered for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('categories'));
    $response->assertOk();
    $response->assertSee('Categories & Data', false);
    $response->assertSee('Groceries');
});

test('user can create a custom category and add data inside it', function () {
    $this->actingAs($this->user);

    Livewire::test(CategoriesManager::class)
        ->set('name', 'Gym & Fitness')
        ->set('type', 'expense')
        ->set('icon', 'heart-pulse')
        ->set('color_hex', '#ef4444')
        ->call('saveCategory')
        ->assertDispatched('notify');

    $category = Category::where('user_id', $this->user->id)
        ->where('name', 'Gym & Fitness')
        ->first();

    expect($category)->not->toBeNull();
    expect($category->type)->toBe('expense');
    expect($category->color_hex)->toBe('#ef4444');

    // Add a transaction inside this category
    $service = app(TransactionService::class);
    $service->create($this->user, [
        'type' => 'expense',
        'wallet_id' => $this->wallet->id,
        'category_id' => $category->id,
        'amount' => 65.00,
        'transaction_date' => now()->toDateString(),
        'payee_merchant' => 'Gold Fitness Club',
    ]);

    // Test CategoriesManager displays data inside the category
    Livewire::test(CategoriesManager::class)
        ->call('viewData', $category->id)
        ->assertSet('viewingCategoryId', $category->id)
        ->assertSet('showDataModal', true)
        ->assertSee('Gold Fitness Club')
        ->assertSee('Gym & Fitness');
});

test('user can delete a custom category that has no transactions', function () {
    $this->actingAs($this->user);

    $category = Category::create([
        'user_id' => $this->user->id,
        'name' => 'Temporary Hobby',
        'type' => 'expense',
        'icon' => 'tag',
        'color_hex' => '#8b5cf6',
        'is_default' => false,
    ]);

    Livewire::test(CategoriesManager::class)
        ->call('deleteCategory', $category->id)
        ->assertDispatched('notify');

    expect(Category::find($category->id))->toBeNull();
});

