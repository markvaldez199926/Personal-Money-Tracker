<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create or retrieve Demo User
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Alex Morgan',
                'password' => Hash::make('password'),
                'currency' => 'USD',
                'currency_symbol' => '$',
            ]
        );

        // 2. Default System Categories
        $defaultCategories = [
            // Expense Categories
            ['name' => 'Food & Dining', 'type' => 'expense', 'icon' => 'utensils', 'color_hex' => '#f97316'],
            ['name' => 'Groceries', 'type' => 'expense', 'icon' => 'shopping-cart', 'color_hex' => '#10b981'],
            ['name' => 'Housing & Rent', 'type' => 'expense', 'icon' => 'home', 'color_hex' => '#6366f1'],
            ['name' => 'Transportation & Gas', 'type' => 'expense', 'icon' => 'car', 'color_hex' => '#0ea5e9'],
            ['name' => 'Utilities & Bills', 'type' => 'expense', 'icon' => 'bolt', 'color_hex' => '#eab308'],
            ['name' => 'Entertainment & Fun', 'type' => 'expense', 'icon' => 'film', 'color_hex' => '#ec4899'],
            ['name' => 'Health & Medical', 'type' => 'expense', 'icon' => 'heart-pulse', 'color_hex' => '#ef4444'],
            ['name' => 'Personal Care & Shopping', 'type' => 'expense', 'icon' => 'bag', 'color_hex' => '#8b5cf6'],
            ['name' => 'Subscriptions', 'type' => 'expense', 'icon' => 'receipt', 'color_hex' => '#14b8a6'],
            ['name' => 'Education', 'type' => 'expense', 'icon' => 'academic-cap', 'color_hex' => '#3b82f6'],

            // Income Categories
            ['name' => 'Primary Salary', 'type' => 'income', 'icon' => 'briefcase', 'color_hex' => '#10b981'],
            ['name' => 'Freelance / Consulting', 'type' => 'income', 'icon' => 'laptop', 'color_hex' => '#06b6d4'],
            ['name' => 'Investments & Dividends', 'type' => 'income', 'icon' => 'trending-up', 'color_hex' => '#8b5cf6'],
            ['name' => 'Gifts & Bonuses', 'type' => 'income', 'icon' => 'gift', 'color_hex' => '#f43f5e'],
        ];

        $categoriesMap = [];
        foreach ($defaultCategories as $cat) {
            $created = Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => $cat['type']],
                [
                    'user_id' => null, // Global default
                    'icon' => $cat['icon'],
                    'color_hex' => $cat['color_hex'],
                    'is_default' => true,
                ]
            );
            $categoriesMap[$cat['name']] = $created;
        }

        // 3. User Wallets
        $walletsData = [
            [
                'name' => 'Physical Cash',
                'type' => 'cash',
                'balance' => 350.00,
                'color_hex' => '#10b981',
                'icon' => 'banknotes',
            ],
            [
                'name' => 'Chase Checking',
                'type' => 'bank',
                'balance' => 4820.50,
                'color_hex' => '#2563eb',
                'icon' => 'building-library',
            ],
            [
                'name' => 'Amex Gold (Credit)',
                'type' => 'credit_card',
                'balance' => 1250.00,
                'color_hex' => '#d97706',
                'icon' => 'credit-card',
            ],
            [
                'name' => 'PayPal / E-Wallet',
                'type' => 'digital_wallet',
                'balance' => 740.25,
                'color_hex' => '#0284c7',
                'icon' => 'device-phone-mobile',
            ],
            [
                'name' => 'High-Yield Savings',
                'type' => 'savings',
                'balance' => 12500.00,
                'color_hex' => '#059669',
                'icon' => 'arrow-trending-up',
            ],
        ];

        $walletsMap = [];
        foreach ($walletsData as $w) {
            $walletsMap[$w['name']] = Wallet::firstOrCreate(
                ['user_id' => $user->id, 'name' => $w['name']],
                [
                    'type' => $w['type'],
                    'balance' => $w['balance'],
                    'color_hex' => $w['color_hex'],
                    'icon' => $w['icon'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Monthly Budgets for Current Month
        $currentMonth = Carbon::now()->format('Y-m');
        $budgetsConfig = [
            ['category' => 'Food & Dining', 'limit' => 450.00, 'threshold' => 80.00],
            ['category' => 'Groceries', 'limit' => 600.00, 'threshold' => 85.00],
            ['category' => 'Transportation & Gas', 'limit' => 200.00, 'threshold' => 80.00],
            ['category' => 'Entertainment & Fun', 'limit' => 150.00, 'threshold' => 75.00],
            ['category' => 'Subscriptions', 'limit' => 80.00, 'threshold' => 90.00],
            ['category' => 'Personal Care & Shopping', 'limit' => 250.00, 'threshold' => 80.00],
        ];

        foreach ($budgetsConfig as $b) {
            if (isset($categoriesMap[$b['category']])) {
                Budget::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'category_id' => $categoriesMap[$b['category']]->id,
                        'month_year' => $currentMonth,
                    ],
                    [
                        'limit_amount' => $b['limit'],
                        'alert_threshold_percentage' => $b['threshold'],
                    ]
                );
            }
        }

        // 5. Sample Transactions for Current Month
        $now = Carbon::now();
        $sampleTransactions = [
            [
                'type' => 'income',
                'wallet' => 'Chase Checking',
                'category' => 'Primary Salary',
                'amount' => 4200.00,
                'date' => $now->copy()->startOfMonth()->toDateString(),
                'payee' => 'Acme Corp Tech',
                'notes' => 'Monthly Salary Deposit',
                'tags' => ['salary', 'income'],
            ],
            [
                'type' => 'income',
                'wallet' => 'PayPal / E-Wallet',
                'category' => 'Freelance / Consulting',
                'amount' => 650.00,
                'date' => $now->copy()->subDays(6)->toDateString(),
                'payee' => 'Client Design Project',
                'notes' => 'Landing page UI kit consultation',
                'tags' => ['freelance', 'side-hustle'],
            ],
            [
                'type' => 'expense',
                'wallet' => 'Chase Checking',
                'category' => 'Housing & Rent',
                'amount' => 1400.00,
                'date' => $now->copy()->startOfMonth()->addDay()->toDateString(),
                'payee' => 'Skyline Apartments',
                'notes' => 'Apartment rent payment',
                'tags' => ['rent', 'fixed'],
            ],
            [
                'type' => 'expense',
                'wallet' => 'Amex Gold (Credit)',
                'category' => 'Groceries',
                'amount' => 142.30,
                'date' => $now->copy()->subDays(3)->toDateString(),
                'payee' => 'Whole Foods Market',
                'notes' => 'Weekly grocery haul & organic produce',
                'tags' => ['groceries', 'household'],
            ],
            [
                'type' => 'expense',
                'wallet' => 'Physical Cash',
                'category' => 'Food & Dining',
                'amount' => 38.50,
                'date' => $now->copy()->subDays(2)->toDateString(),
                'payee' => 'Italian Trattoria',
                'notes' => 'Dinner with team',
                'tags' => ['dining', 'weekend'],
            ],
            [
                'type' => 'expense',
                'wallet' => 'Amex Gold (Credit)',
                'category' => 'Food & Dining',
                'amount' => 18.25,
                'date' => $now->copy()->subDays(1)->toDateString(),
                'payee' => 'Artisan Coffee Roasters',
                'notes' => 'Espresso & pastry',
                'tags' => ['coffee'],
            ],
            [
                'type' => 'expense',
                'wallet' => 'Amex Gold (Credit)',
                'category' => 'Subscriptions',
                'amount' => 19.99,
                'date' => $now->copy()->subDays(5)->toDateString(),
                'payee' => 'Netflix Premium 4K',
                'notes' => 'Family plan monthly subscription',
                'tags' => ['subscription', 'entertainment'],
            ],
            [
                'type' => 'expense',
                'wallet' => 'Chase Checking',
                'category' => 'Transportation & Gas',
                'amount' => 54.00,
                'date' => $now->copy()->subDays(4)->toDateString(),
                'payee' => 'Shell Fuel Station',
                'notes' => 'Full tank premium gas',
                'tags' => ['commute', 'gas'],
            ],
            [
                'type' => 'expense',
                'wallet' => 'PayPal / E-Wallet',
                'category' => 'Entertainment & Fun',
                'amount' => 35.00,
                'date' => $now->copy()->toDateString(),
                'payee' => 'Steam Games',
                'notes' => 'Weekend indie game release',
                'tags' => ['gaming', 'leisure'],
            ],
        ];

        foreach ($sampleTransactions as $tx) {
            Transaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'payee_merchant' => $tx['payee'],
                    'transaction_date' => $tx['date'],
                    'amount' => $tx['amount'],
                ],
                [
                    'wallet_id' => $walletsMap[$tx['wallet']]->id,
                    'category_id' => $categoriesMap[$tx['category']]->id,
                    'type' => $tx['type'],
                    'notes' => $tx['notes'],
                    'tags' => $tx['tags'],
                ]
            );
        }

        // 6. Recurring Transactions
        $recurringData = [
            [
                'name' => 'Monthly Salary',
                'type' => 'income',
                'amount' => 4200.00,
                'wallet' => 'Chase Checking',
                'category' => 'Primary Salary',
                'frequency' => 'monthly',
                'start_date' => $now->copy()->startOfMonth()->toDateString(),
                'next_due_date' => $now->copy()->startOfMonth()->addMonth()->toDateString(),
                'auto_post' => true,
            ],
            [
                'name' => 'Apartment Rent',
                'type' => 'expense',
                'amount' => 1400.00,
                'wallet' => 'Chase Checking',
                'category' => 'Housing & Rent',
                'frequency' => 'monthly',
                'start_date' => $now->copy()->startOfMonth()->toDateString(),
                'next_due_date' => $now->copy()->startOfMonth()->addMonth()->toDateString(),
                'auto_post' => true,
            ],
            [
                'name' => 'Netflix Premium',
                'type' => 'expense',
                'amount' => 19.99,
                'wallet' => 'Amex Gold (Credit)',
                'category' => 'Subscriptions',
                'frequency' => 'monthly',
                'start_date' => $now->copy()->subDays(5)->toDateString(),
                'next_due_date' => $now->copy()->subDays(5)->addMonth()->toDateString(),
                'auto_post' => true,
            ],
            [
                'name' => 'Spotify Family',
                'type' => 'expense',
                'amount' => 16.99,
                'wallet' => 'Amex Gold (Credit)',
                'category' => 'Subscriptions',
                'frequency' => 'monthly',
                'start_date' => $now->copy()->subDays(12)->toDateString(),
                'next_due_date' => $now->copy()->subDays(12)->addMonth()->toDateString(),
                'auto_post' => true,
            ],
        ];

        foreach ($recurringData as $r) {
            RecurringTransaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $r['name'],
                ],
                [
                    'wallet_id' => $walletsMap[$r['wallet']]->id,
                    'category_id' => $categoriesMap[$r['category']]->id,
                    'type' => $r['type'],
                    'amount' => $r['amount'],
                    'frequency' => $r['frequency'],
                    'start_date' => $r['start_date'],
                    'next_due_date' => $r['next_due_date'],
                    'auto_post' => $r['auto_post'],
                    'is_active' => true,
                ]
            );
        }
    }
}
