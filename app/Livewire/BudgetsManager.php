<?php

namespace App\Livewire;

use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class BudgetsManager extends Component
{
    public string $selected_month = '';
    public bool $showBudgetModal = false;
    public ?int $editingBudgetId = null;

    public ?int $category_id = null;
    public string $limit_amount = '';
    public string $alert_threshold_percentage = '80.00';

    protected function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'limit_amount' => 'required|numeric|min:1',
            'alert_threshold_percentage' => 'required|numeric|min:10|max:100',
        ];
    }

    public function mount(): void
    {
        $this->selected_month = Carbon::now()->format('Y-m');
    }

    #[On('transaction-saved')]
    public function refreshBudgets(): void
    {
        // Re-render when transactions change
    }

    public function openNewBudgetModal(): void
    {
        $this->resetValidation();
        $this->reset(['editingBudgetId', 'limit_amount']);
        $this->alert_threshold_percentage = '80.00';

        // Pick first unbudgeted category
        $user = Auth::user();
        $existingCatIds = Budget::where('user_id', $user->id)
            ->where('month_year', $this->selected_month)
            ->pluck('category_id')
            ->toArray();

        $firstAvailable = Category::forUser($user->id)
            ->expenses()
            ->whereNotIn('id', $existingCatIds)
            ->first();

        $this->category_id = $firstAvailable ? $firstAvailable->id : null;
        $this->showBudgetModal = true;
    }

    public function openEditBudgetModal(int $id): void
    {
        $this->resetValidation();
        $budget = Budget::where('user_id', Auth::id())->findOrFail($id);
        $this->editingBudgetId = $budget->id;
        $this->category_id = $budget->category_id;
        $this->limit_amount = (string) $budget->limit_amount;
        $this->alert_threshold_percentage = (string) $budget->alert_threshold_percentage;
        $this->showBudgetModal = true;
    }

    public function saveBudget(): void
    {
        $this->validate();

        $user = Auth::user();

        if ($this->editingBudgetId) {
            $budget = Budget::where('user_id', $user->id)->findOrFail($this->editingBudgetId);
            $budget->update([
                'category_id' => $this->category_id,
                'limit_amount' => $this->limit_amount,
                'alert_threshold_percentage' => $this->alert_threshold_percentage,
            ]);
            $this->dispatch('notify', message: 'Budget updated successfully.');
        } else {
            Budget::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'category_id' => $this->category_id,
                    'month_year' => $this->selected_month,
                ],
                [
                    'limit_amount' => $this->limit_amount,
                    'alert_threshold_percentage' => $this->alert_threshold_percentage,
                ]
            );
            $this->dispatch('notify', message: 'Category budget configured.');
        }

        $this->showBudgetModal = false;
        $this->dispatch('transaction-saved');
    }

    public function copyPreviousMonthBudgets(): void
    {
        $user = Auth::user();
        $prevMonth = Carbon::createFromFormat('Y-m', $this->selected_month)->subMonth()->format('Y-m');

        $previousBudgets = Budget::where('user_id', $user->id)
            ->where('month_year', $prevMonth)
            ->get();

        if ($previousBudgets->isEmpty()) {
            $this->dispatch('notify', message: 'No budgets found from the previous month to copy.', type: 'info');
            return;
        }

        $copiedCount = 0;
        foreach ($previousBudgets as $prev) {
            Budget::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'category_id' => $prev->category_id,
                    'month_year' => $this->selected_month,
                ],
                [
                    'limit_amount' => $prev->limit_amount,
                    'alert_threshold_percentage' => $prev->alert_threshold_percentage,
                ]
            );
            $copiedCount++;
        }

        $this->dispatch('notify', message: "Copied {$copiedCount} budgets from previous month.");
        $this->dispatch('transaction-saved');
    }

    public function deleteBudget(int $id): void
    {
        $budget = Budget::where('user_id', Auth::id())->findOrFail($id);
        $budget->delete();

        $this->dispatch('notify', message: 'Budget removed.');
        $this->dispatch('transaction-saved');
    }

    public function render()
    {
        $user = Auth::user();

        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month_year', $this->selected_month)
            ->get();

        $totalBudgeted = $budgets->sum('limit_amount');
        $totalSpent = $budgets->sum(fn($b) => $b->spent_amount);
        $totalRemaining = max(0, $totalBudgeted - $totalSpent);
        $overallPercentage = $totalBudgeted > 0 ? round(($totalSpent / $totalBudgeted) * 100, 1) : 0;

        $categories = Category::forUser($user->id)->expenses()->get();

        return view('livewire.budgets-manager', [
            'budgets' => $budgets,
            'totalBudgeted' => $totalBudgeted,
            'totalSpent' => $totalSpent,
            'totalRemaining' => $totalRemaining,
            'overallPercentage' => $overallPercentage,
            'categories' => $categories,
        ]);
    }
}
