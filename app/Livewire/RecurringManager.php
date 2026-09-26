<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Wallet;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class RecurringManager extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $type = 'expense';
    public string $amount = '';
    public string $frequency = 'monthly';
    public string $start_date = '';
    public string $next_due_date = '';
    public ?int $wallet_id = null;
    public ?int $category_id = null;
    public bool $auto_post = true;
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'type' => 'required|in:expense,income',
            'amount' => 'required|numeric|min:0.01',
            'frequency' => 'required|in:daily,weekly,biweekly,monthly,yearly',
            'start_date' => 'required|date',
            'next_due_date' => 'required|date',
            'wallet_id' => 'required|exists:wallets,id',
            'category_id' => 'nullable|exists:categories,id',
            'auto_post' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function mount(): void
    {
        $this->start_date = Carbon::today()->toDateString();
        $this->next_due_date = Carbon::today()->addMonth()->toDateString();
    }

    #[On('transaction-saved')]
    public function refreshList(): void
    {
        // Refresh when updated
    }

    public function openNewModal(): void
    {
        $this->resetValidation();
        $this->reset(['editingId', 'name', 'amount', 'notes']);
        $this->type = 'expense';
        $this->frequency = 'monthly';
        $this->start_date = Carbon::today()->toDateString();
        $this->next_due_date = Carbon::today()->addMonth()->toDateString();
        $this->auto_post = true;

        $user = Auth::user();
        $firstWallet = $user->wallets()->active()->first();
        $this->wallet_id = $firstWallet?->id;

        $firstCat = Category::forUser($user->id)->expenses()->first();
        $this->category_id = $firstCat?->id;

        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $item = RecurringTransaction::where('user_id', Auth::id())->findOrFail($id);
        $this->editingId = $item->id;
        $this->name = $item->name;
        $this->type = $item->type;
        $this->amount = (string) $item->amount;
        $this->frequency = $item->frequency;
        $this->start_date = $item->start_date->toDateString();
        $this->next_due_date = $item->next_due_date->toDateString();
        $this->wallet_id = $item->wallet_id;
        $this->category_id = $item->category_id;
        $this->auto_post = $item->auto_post;
        $this->notes = $item->notes ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();

        if ($this->editingId) {
            $item = RecurringTransaction::where('user_id', $user->id)->findOrFail($this->editingId);
            $item->update([
                'name' => $this->name,
                'type' => $this->type,
                'amount' => $this->amount,
                'frequency' => $this->frequency,
                'start_date' => $this->start_date,
                'next_due_date' => $this->next_due_date,
                'wallet_id' => $this->wallet_id,
                'category_id' => $this->category_id,
                'auto_post' => $this->auto_post,
                'notes' => $this->notes ?: null,
            ]);
            $this->dispatch('notify', message: 'Recurring transaction updated.');
        } else {
            RecurringTransaction::create([
                'user_id' => $user->id,
                'name' => $this->name,
                'type' => $this->type,
                'amount' => $this->amount,
                'frequency' => $this->frequency,
                'start_date' => $this->start_date,
                'next_due_date' => $this->next_due_date,
                'wallet_id' => $this->wallet_id,
                'category_id' => $this->category_id,
                'auto_post' => $this->auto_post,
                'notes' => $this->notes ?: null,
                'is_active' => true,
            ]);
            $this->dispatch('notify', message: 'Recurring schedule created.');
        }

        $this->showModal = false;
        $this->dispatch('transaction-saved');
    }

    public function toggleActive(int $id): void
    {
        $item = RecurringTransaction::where('user_id', Auth::id())->findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);

        $this->dispatch('notify', message: 'Status updated.');
    }

    public function toggleAutoPost(int $id): void
    {
        $item = RecurringTransaction::where('user_id', Auth::id())->findOrFail($id);
        $item->update(['auto_post' => ! $item->auto_post]);

        $this->dispatch('notify', message: 'Auto-post setting updated.');
    }

    public function postNow(int $id, TransactionService $service): void
    {
        $item = RecurringTransaction::where('user_id', Auth::id())->findOrFail($id);
        $user = Auth::user();

        $service->create($user, [
            'wallet_id' => $item->wallet_id,
            'category_id' => $item->category_id,
            'type' => $item->type,
            'amount' => $item->amount,
            'transaction_date' => Carbon::today()->toDateString(),
            'payee_merchant' => $item->name,
            'notes' => 'Manually posted recurring: ' . ($item->notes ?? $item->name),
            'tags' => ['recurring', 'manual-post'],
        ]);

        $nextDate = $item->getNextComputedDueDate();
        $item->update([
            'last_posted_at' => Carbon::now(),
            'next_due_date' => $nextDate->toDateString(),
        ]);

        $this->dispatch('notify', message: "Posted {$item->name} and rolled due date to {$nextDate->format('M d, Y')}.");
        $this->dispatch('transaction-saved');
    }

    public function deleteRecurring(int $id): void
    {
        $item = RecurringTransaction::where('user_id', Auth::id())->findOrFail($id);
        $item->delete();

        $this->dispatch('notify', message: 'Recurring schedule deleted.');
        $this->dispatch('transaction-saved');
    }

    public function render()
    {
        $user = Auth::user();
        $recurringItems = RecurringTransaction::with(['wallet', 'category'])
            ->where('user_id', $user->id)
            ->orderBy('next_due_date', 'asc')
            ->get();

        $monthlyExpenses = $recurringItems->where('is_active', true)->where('type', 'expense')->sum(function ($item) {
            return match ($item->frequency) {
                'daily' => $item->amount * 30,
                'weekly' => $item->amount * 4.33,
                'biweekly' => $item->amount * 2.16,
                'monthly' => $item->amount,
                'yearly' => $item->amount / 12,
                default => $item->amount,
            };
        });

        $monthlyIncome = $recurringItems->where('is_active', true)->where('type', 'income')->sum(function ($item) {
            return match ($item->frequency) {
                'daily' => $item->amount * 30,
                'weekly' => $item->amount * 4.33,
                'biweekly' => $item->amount * 2.16,
                'monthly' => $item->amount,
                'yearly' => $item->amount / 12,
                default => $item->amount,
            };
        });

        $wallets = $user->wallets()->active()->get();
        $categories = Category::forUser($user->id)->get();

        return view('livewire.recurring-manager', [
            'recurringItems' => $recurringItems,
            'monthlyExpenses' => $monthlyExpenses,
            'monthlyIncome' => $monthlyIncome,
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }
}
