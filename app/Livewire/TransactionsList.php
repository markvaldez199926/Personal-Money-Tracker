<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionsList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public ?int $wallet_id = null;
    public ?int $category_id = null;
    public string $date_range = 'month'; // 'month', 'last_month', 'all', 'custom'
    public string $start_date = '';
    public string $end_date = '';

    // Receipt preview modal state
    public ?string $previewReceiptUrl = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'wallet_id' => ['except' => null],
        'category_id' => ['except' => null],
        'date_range' => ['except' => 'month'],
    ];

    public function mount(): void
    {
        $this->start_date = Carbon::now()->startOfMonth()->toDateString();
        $this->end_date = Carbon::now()->endOfMonth()->toDateString();
    }

    public function updatedDateRange(): void
    {
        if ($this->date_range === 'month') {
            $this->start_date = Carbon::now()->startOfMonth()->toDateString();
            $this->end_date = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($this->date_range === 'last_month') {
            $this->start_date = Carbon::now()->subMonth()->startOfMonth()->toDateString();
            $this->end_date = Carbon::now()->subMonth()->endOfMonth()->toDateString();
        } elseif ($this->date_range === 'all') {
            $this->start_date = '';
            $this->end_date = '';
        }
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedWalletId(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    #[On('transaction-saved')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function showReceipt(string $url): void
    {
        $this->previewReceiptUrl = $url;
    }

    public function closeReceiptModal(): void
    {
        $this->previewReceiptUrl = null;
    }

    public function deleteTransaction(int $id, TransactionService $service): void
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $service->delete($transaction);

        $this->dispatch('transaction-saved');
        $this->dispatch('notify', message: 'Transaction deleted successfully.');
    }

    public function exportCsv(): StreamedResponse
    {
        $user = Auth::user();
        $query = $this->buildQuery();
        $transactions = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions_' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($transactions, $user) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date', 'Type', 'Amount', 'Currency', 'Category', 'Wallet', 'To Wallet', 'Payee/Merchant', 'Notes', 'Tags']);

            foreach ($transactions as $tx) {
                fputcsv($handle, [
                    $tx->id,
                    $tx->transaction_date->toDateString(),
                    $tx->type,
                    $tx->amount,
                    $user->currency,
                    $tx->category?->name ?? 'N/A',
                    $tx->wallet?->name ?? 'N/A',
                    $tx->toWallet?->name ?? 'N/A',
                    $tx->payee_merchant,
                    $tx->notes,
                    is_array($tx->tags) ? implode(', ', $tx->tags) : '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    private function buildQuery()
    {
        $query = Transaction::with(['wallet', 'category', 'toWallet'])
            ->where('user_id', Auth::id());

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('payee_merchant', 'like', "%{$this->search}%")
                  ->orWhere('notes', 'like', "%{$this->search}%");
            });
        }

        if (! empty($this->type)) {
            $query->where('type', $this->type);
        }

        if ($this->wallet_id) {
            $query->where(function ($q) {
                $q->where('wallet_id', $this->wallet_id)
                  ->orWhere('to_wallet_id', $this->wallet_id);
            });
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('transaction_date', [$this->start_date, $this->end_date]);
        }

        return $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');
    }

    public function render()
    {
        $transactions = $this->buildQuery()->paginate(15);
        $wallets = Auth::user()->wallets()->active()->get();
        $categories = Category::forUser(Auth::id())->get();

        return view('livewire.transactions-list', [
            'transactions' => $transactions,
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }
}
