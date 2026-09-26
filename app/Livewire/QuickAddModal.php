<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Wallet;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class QuickAddModal extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;

    public string $type = 'expense'; // expense, income, transfer
    public ?string $amount = '';
    public ?int $wallet_id = null;
    public ?int $to_wallet_id = null;
    public ?int $category_id = null;
    public string $transaction_date = '';
    public string $payee_merchant = '';
    public string $notes = '';
    public string $tag_input = '';
    public $receipt = null;

    protected function rules(): array
    {
        return [
            'type' => 'required|in:expense,income,transfer',
            'amount' => 'required|numeric|min:0.01',
            'wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id' => 'nullable|required_if:type,transfer|different:wallet_id|exists:wallets,id',
            'category_id' => 'nullable|required_unless:type,transfer|exists:categories,id',
            'transaction_date' => 'required|date',
            'payee_merchant' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'receipt' => 'nullable|image|max:5120', // 5MB max
        ];
    }

    public function mount(): void
    {
        $this->transaction_date = Carbon::today()->toDateString();
        $this->setDefaultWallet();
    }

    public function setDefaultWallet(): void
    {
        if (Auth::check() && ! $this->wallet_id) {
            $firstWallet = Auth::user()->wallets()->active()->first();
            if ($firstWallet) {
                $this->wallet_id = $firstWallet->id;
            }
        }
    }

    #[On('open-quick-add')]
    public function openModal(?string $type = null): void
    {
        $this->resetValidation();
        if ($type && in_array($type, ['expense', 'income', 'transfer'])) {
            $this->type = $type;
        }
        $this->transaction_date = Carbon::today()->toDateString();
        $this->setDefaultWallet();
        $this->setDefaultCategory();
        $this->isOpen = true;
    }

    public function updatedType(): void
    {
        $this->setDefaultCategory();
    }

    public function setDefaultCategory(): void
    {
        if ($this->type !== 'transfer' && Auth::check()) {
            $cat = Category::forUser(Auth::id())
                ->where('type', $this->type)
                ->first();
            $this->category_id = $cat ? $cat->id : null;
        } else {
            $this->category_id = null;
        }
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->reset(['amount', 'payee_merchant', 'notes', 'tag_input', 'receipt']);
        $this->resetValidation();
    }

    public function save(TransactionService $service): void
    {
        $this->validate();

        $user = Auth::user();

        // Process comma-separated tags into array
        $tags = [];
        if (! empty($this->tag_input)) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $this->tag_input))));
        }

        $service->create($user, [
            'wallet_id' => $this->wallet_id,
            'to_wallet_id' => $this->type === 'transfer' ? $this->to_wallet_id : null,
            'category_id' => $this->type !== 'transfer' ? $this->category_id : null,
            'type' => $this->type,
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date,
            'payee_merchant' => $this->payee_merchant ?: null,
            'notes' => $this->notes ?: null,
            'tags' => $tags,
        ], $this->receipt);

        $this->dispatch('transaction-saved');
        $this->dispatch('notify', message: 'Transaction recorded successfully!');
        $this->closeModal();
    }

    public function render()
    {
        $wallets = Auth::check() ? Auth::user()->wallets()->active()->get() : collect();
        $categories = Auth::check() && $this->type !== 'transfer'
            ? Category::forUser(Auth::id())->where('type', $this->type)->get()
            : collect();

        return view('livewire.quick-add-modal', [
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }
}
