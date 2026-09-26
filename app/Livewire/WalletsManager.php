<?php

namespace App\Livewire;

use App\Models\Wallet;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class WalletsManager extends Component
{
    public bool $showWalletModal = false;
    public bool $showTransferModal = false;
    public ?int $editingWalletId = null;

    // Wallet form fields
    public string $name = '';
    public string $type = 'bank';
    public string $balance = '0.00';
    public string $color_hex = '#2563eb';
    public string $icon = 'building-library';
    public string $notes = '';
    public bool $is_active = true;

    // Transfer form fields
    public ?int $from_wallet_id = null;
    public ?int $to_wallet_id = null;
    public string $transfer_amount = '';
    public string $transfer_notes = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank,credit_card,digital_wallet,savings',
            'balance' => 'required|numeric',
            'color_hex' => 'required|string|max:20',
            'icon' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    #[On('transaction-saved')]
    public function refreshWallets(): void
    {
        // Re-render wallets on updates
    }

    public function openNewWalletModal(): void
    {
        $this->resetValidation();
        $this->reset(['editingWalletId', 'name', 'notes']);
        $this->type = 'bank';
        $this->balance = '0.00';
        $this->color_hex = '#2563eb';
        $this->icon = 'building-library';
        $this->is_active = true;
        $this->showWalletModal = true;
    }

    public function openEditWalletModal(int $id): void
    {
        $this->resetValidation();
        $wallet = Wallet::where('user_id', Auth::id())->findOrFail($id);
        $this->editingWalletId = $wallet->id;
        $this->name = $wallet->name;
        $this->type = $wallet->type;
        $this->balance = (string) $wallet->balance;
        $this->color_hex = $wallet->color_hex;
        $this->icon = $wallet->icon;
        $this->notes = $wallet->notes ?? '';
        $this->is_active = $wallet->is_active;
        $this->showWalletModal = true;
    }

    public function saveWallet(): void
    {
        $this->validate();

        $user = Auth::user();

        if ($this->editingWalletId) {
            $wallet = Wallet::where('user_id', $user->id)->findOrFail($this->editingWalletId);
            $wallet->update([
                'name' => $this->name,
                'type' => $this->type,
                'balance' => $this->balance,
                'color_hex' => $this->color_hex,
                'icon' => $this->icon,
                'notes' => $this->notes ?: null,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('notify', message: 'Wallet updated successfully.');
        } else {
            Wallet::create([
                'user_id' => $user->id,
                'name' => $this->name,
                'type' => $this->type,
                'balance' => $this->balance,
                'color_hex' => $this->color_hex,
                'icon' => $this->icon,
                'notes' => $this->notes ?: null,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('notify', message: 'Wallet created successfully.');
        }

        $this->showWalletModal = false;
        $this->dispatch('transaction-saved');
    }

    public function openTransferModal(): void
    {
        $this->resetValidation();
        $wallets = Auth::user()->wallets()->active()->get();
        if ($wallets->count() >= 2) {
            $this->from_wallet_id = $wallets[0]->id;
            $this->to_wallet_id = $wallets[1]->id;
        }
        $this->transfer_amount = '';
        $this->transfer_notes = '';
        $this->showTransferModal = true;
    }

    public function executeTransfer(TransactionService $service): void
    {
        $this->validate([
            'from_wallet_id' => 'required|different:to_wallet_id|exists:wallets,id',
            'to_wallet_id' => 'required|exists:wallets,id',
            'transfer_amount' => 'required|numeric|min:0.01',
            'transfer_notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        $service->create($user, [
            'wallet_id' => $this->from_wallet_id,
            'to_wallet_id' => $this->to_wallet_id,
            'type' => 'transfer',
            'amount' => $this->transfer_amount,
            'transaction_date' => Carbon::today()->toDateString(),
            'notes' => $this->transfer_notes ?: 'Wallet Transfer',
            'tags' => ['transfer'],
        ]);

        $this->showTransferModal = false;
        $this->dispatch('transaction-saved');
        $this->dispatch('notify', message: 'Funds transferred successfully.');
    }

    public function deleteWallet(int $id): void
    {
        $wallet = Wallet::where('user_id', Auth::id())->findOrFail($id);

        if ($wallet->transactions()->exists()) {
            // Soft-deactivate if it has transaction history to maintain data integrity
            $wallet->update(['is_active' => false]);
            $this->dispatch('notify', message: 'Wallet deactivated (archived) to preserve transaction history.', type: 'info');
        } else {
            $wallet->delete();
            $this->dispatch('notify', message: 'Wallet deleted permanently.');
        }

        $this->dispatch('transaction-saved');
    }

    public function render()
    {
        $wallets = Auth::user()->wallets()->orderBy('is_active', 'desc')->get();
        $totalBalance = $wallets->where('is_active', true)->sum('balance');

        return view('livewire.wallets-manager', [
            'wallets' => $wallets,
            'totalBalance' => $totalBalance,
        ]);
    }
}
