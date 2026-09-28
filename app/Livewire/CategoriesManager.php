<?php

namespace App\Livewire;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoriesManager extends Component
{
    public string $typeFilter = 'all'; // 'all', 'expense', 'income'
    public string $search = '';

    // Create / Edit Category Modal state
    public bool $showCategoryModal = false;
    public ?int $editingCategoryId = null;
    public string $name = '';
    public string $type = 'expense';
    public string $icon = 'shopping-cart';
    public string $color_hex = '#6366f1';

    // View Category Data Modal state
    public bool $showDataModal = false;
    public ?int $viewingCategoryId = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:60',
            'type' => 'required|in:expense,income',
            'icon' => 'required|string|max:50',
            'color_hex' => 'required|string|max:20',
        ];
    }

    #[On('transaction-saved')]
    public function refreshCategories(): void
    {
        // Re-render when transactions are saved
    }

    public function openNewCategoryModal(?string $defaultType = 'expense'): void
    {
        $this->resetValidation();
        $this->reset(['editingCategoryId', 'name']);
        $this->type = in_array($defaultType, ['expense', 'income']) ? $defaultType : 'expense';
        $this->icon = $this->type === 'expense' ? 'shopping-cart' : 'briefcase';
        $this->color_hex = $this->type === 'expense' ? '#f97316' : '#10b981';
        $this->showCategoryModal = true;
    }

    public function editCategory(int $id): void
    {
        $this->resetValidation();
        $user = Auth::user();
        $cat = Category::forUser($user->id)->findOrFail($id);

        $this->editingCategoryId = $cat->id;
        $this->name = $cat->name;
        $this->type = $cat->type;
        $this->icon = $cat->icon;
        $this->color_hex = $cat->color_hex;
        $this->showCategoryModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate();
        $user = Auth::user();

        if ($this->editingCategoryId) {
            $cat = Category::where('id', $this->editingCategoryId)
                ->where('user_id', $user->id)
                ->first();

            if ($cat) {
                $cat->update([
                    'name' => $this->name,
                    'type' => $this->type,
                    'icon' => $this->icon,
                    'color_hex' => $this->color_hex,
                ]);
                $this->dispatch('notify', message: "Category '{$cat->name}' updated successfully!");
            } else {
                // If it was a system category, create a custom duplicate for the user
                $newCat = Category::create([
                    'user_id' => $user->id,
                    'name' => $this->name,
                    'type' => $this->type,
                    'icon' => $this->icon,
                    'color_hex' => $this->color_hex,
                    'is_default' => false,
                ]);
                $this->dispatch('notify', message: "Custom category '{$newCat->name}' created!");
            }
        } else {
            $cat = Category::create([
                'user_id' => $user->id,
                'name' => $this->name,
                'type' => $this->type,
                'icon' => $this->icon,
                'color_hex' => $this->color_hex,
                'is_default' => false,
            ]);
            $this->dispatch('notify', message: "Category '{$cat->name}' added successfully!");
        }

        $this->showCategoryModal = false;
        $this->reset(['editingCategoryId', 'name']);
    }

    public function deleteCategory(int $id): void
    {
        $user = Auth::user();
        $cat = Category::where('id', $id)->first();

        if (! $cat) {
            return;
        }

        if ($cat->is_default || $cat->user_id === null) {
            $this->dispatch('notify', message: 'Default system categories cannot be deleted.', type: 'warning');
            return;
        }

        if ($cat->user_id !== $user->id) {
            abort(403);
        }

        // Check if there are user transactions in this category
        $txCount = Transaction::where('category_id', $cat->id)->where('user_id', $user->id)->count();
        if ($txCount > 0) {
            $this->dispatch('notify', message: "Cannot delete '{$cat->name}' because it contains {$txCount} transaction(s). Reassign or delete transactions first.", type: 'warning');
            return;
        }

        $catName = $cat->name;
        $cat->delete();
        $this->dispatch('notify', message: "Category '{$catName}' deleted.");
    }

    public function viewData(int $id): void
    {
        $this->viewingCategoryId = $id;
        $this->showDataModal = true;
    }

    public function closeDataModal(): void
    {
        $this->showDataModal = false;
        $this->viewingCategoryId = null;
    }

    public function addTransaction(int $categoryId): void
    {
        $cat = Category::forUser(Auth::id())->find($categoryId);
        if ($cat) {
            $this->dispatch('open-quick-add', type: $cat->type, categoryId: $cat->id);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $currentMonth = Carbon::now()->format('Y-m');

        $categories = Category::forUser($user->id)
            ->withCount(['transactions as user_transactions_count' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->withSum(['transactions as total_amount' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }], 'amount')
            ->withSum(['transactions as month_amount' => function ($q) use ($user, $startOfMonth, $endOfMonth) {
                $q->where('user_id', $user->id)->whereBetween('transaction_date', [$startOfMonth, $endOfMonth]);
            }], 'amount')
            ->when($this->typeFilter !== 'all', fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->search !== '', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $budgets = Budget::where('user_id', $user->id)
            ->where('month_year', $currentMonth)
            ->get()
            ->keyBy('category_id');

        $viewingCategory = null;
        $categoryTransactions = collect();
        if ($this->viewingCategoryId) {
            $viewingCategory = Category::forUser($user->id)->find($this->viewingCategoryId);
            if ($viewingCategory) {
                $categoryTransactions = Transaction::with('wallet')
                    ->where('user_id', $user->id)
                    ->where('category_id', $viewingCategory->id)
                    ->orderBy('transaction_date', 'desc')
                    ->take(25)
                    ->get();
            }
        }

        // Summary counts
        $totalCategoriesCount = Category::forUser($user->id)->count();
        $expenseCategoriesCount = Category::forUser($user->id)->expenses()->count();
        $incomeCategoriesCount = Category::forUser($user->id)->income()->count();
        $categorizedTransactionsCount = Transaction::where('user_id', $user->id)->whereNotNull('category_id')->count();

        return view('livewire.categories-manager', [
            'categories' => $categories,
            'budgets' => $budgets,
            'viewingCategory' => $viewingCategory,
            'categoryTransactions' => $categoryTransactions,
            'totalCategoriesCount' => $totalCategoriesCount,
            'expenseCategoriesCount' => $expenseCategoriesCount,
            'incomeCategoriesCount' => $incomeCategoriesCount,
            'categorizedTransactionsCount' => $categorizedTransactionsCount,
        ]);
    }
}
