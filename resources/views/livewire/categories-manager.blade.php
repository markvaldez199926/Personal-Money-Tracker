<div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Categories & Data</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Organize your expenses and income, add custom categories, and explore all data inside each category.</p>
        </div>
        <div class="flex items-center gap-2">
            <button
                wire:click="openNewCategoryModal('expense')"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-indigo-500/20 transition active:scale-95"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>New Category</span>
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <x-icon name="tag" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Categories</p>
                <p class="text-xl font-black text-gray-900 dark:text-white">{{ $totalCategoriesCount }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <x-icon name="arrow-trending-down" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expense Types</p>
                <p class="text-xl font-black text-gray-900 dark:text-white">{{ $expenseCategoriesCount }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <x-icon name="arrow-trending-up" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Income Types</p>
                <p class="text-xl font-black text-gray-900 dark:text-white">{{ $incomeCategoriesCount }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <x-icon name="receipt" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categorized Tx</p>
                <p class="text-xl font-black text-gray-900 dark:text-white">{{ $categorizedTransactionsCount }}</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white dark:bg-gray-800 p-3 sm:p-4 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <!-- Type Tabs -->
        <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-700/60 rounded-xl text-xs font-semibold">
            <button
                type="button"
                wire:click="$set('typeFilter', 'all')"
                class="px-3 py-1.5 rounded-lg transition {{ $typeFilter === 'all' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
            >
                All ({{ $totalCategoriesCount }})
            </button>
            <button
                type="button"
                wire:click="$set('typeFilter', 'expense')"
                class="px-3 py-1.5 rounded-lg transition {{ $typeFilter === 'expense' ? 'bg-white dark:bg-gray-800 text-rose-600 dark:text-rose-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
            >
                Expenses ({{ $expenseCategoriesCount }})
            </button>
            <button
                type="button"
                wire:click="$set('typeFilter', 'income')"
                class="px-3 py-1.5 rounded-lg transition {{ $typeFilter === 'income' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
            >
                Income ({{ $incomeCategoriesCount }})
            </button>
        </div>

        <!-- Search Input -->
        <div class="relative w-full sm:w-64">
            <input
                type="text"
                wire:model.live.debounce.250ms="search"
                placeholder="Search categories..."
                class="w-full pl-9 pr-3 py-1.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Category Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($categories as $category)
            @php
                $txCount = $category->user_transactions_count ?? 0;
                $totalSum = (float)($category->total_amount ?? 0);
                $monthSum = (float)($category->month_amount ?? 0);
                $isExpense = $category->type === 'expense';
                $budget = $budgets[$category->id] ?? null;
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between group relative overflow-hidden">
                <!-- Top Color Accent Bar -->
                <div class="absolute top-0 left-0 right-0 h-1" style="background-color: {{ $category->color_hex }}"></div>

                <div>
                    <!-- Category Header -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-11 h-11 rounded-xl flex items-center justify-center text-white shrink-0 shadow-sm"
                                style="background-color: {{ $category->color_hex }}"
                            >
                                <x-icon :name="$category->icon" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-sm sm:text-base text-gray-900 dark:text-white leading-tight">
                                    {{ $category->name }}
                                </h3>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $isExpense ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                    @if($category->is_default || $category->user_id === null)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                            Default
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                            Custom
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Menu / Buttons -->
                        <div class="flex items-center gap-1">
                            @if(!$category->is_default && $category->user_id === auth()->id())
                                <button
                                    wire:click="editCategory({{ $category->id }})"
                                    class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition"
                                    title="Edit Category"
                                >
                                    <x-icon name="pencil" class="w-3.5 h-3.5" />
                                </button>
                                <button
                                    wire:click="deleteCategory({{ $category->id }})"
                                    wire:confirm="Are you sure you want to delete '{{ $category->name }}'?"
                                    class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition"
                                    title="Delete Category"
                                >
                                    <x-icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Category Data Metrics -->
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-3 my-3 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-gray-400 dark:text-gray-500 block text-[10px] uppercase font-semibold">Total Spent / Recv</span>
                            <span class="font-bold text-gray-900 dark:text-white text-sm">
                                {{ auth()->user()->formatMoney($totalSum) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-400 dark:text-gray-500 block text-[10px] uppercase font-semibold">This Month</span>
                            <span class="font-bold {{ $isExpense ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} text-sm">
                                {{ auth()->user()->formatMoney($monthSum) }}
                            </span>
                        </div>
                    </div>

                    <!-- Linked Budget Status (if exists) -->
                    @if($budget)
                        @php
                            $spent = $monthSum;
                            $limit = (float)$budget->limit_amount;
                            $pct = $limit > 0 ? min(100, round(($spent / $limit) * 100)) : 0;
                            $isOver = $spent > $limit;
                        @endphp
                        <div class="mt-2 mb-3 p-2.5 rounded-xl border border-gray-100 dark:border-gray-700/60 bg-white/50 dark:bg-gray-800/50">
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="text-gray-500 dark:text-gray-400 font-medium">Monthly Budget</span>
                                <span class="font-bold {{ $isOver ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $pct }}% of {{ auth()->user()->formatMoney($limit) }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all {{ $isOver ? 'bg-red-500' : ($pct > 80 ? 'bg-amber-500' : 'bg-indigo-500') }}"
                                    style="width: {{ $pct }}%"
                                ></div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Bottom Interactive Actions -->
                <div class="pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-2 mt-2">
                    <button
                        type="button"
                        wire:click="viewData({{ $category->id }})"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition"
                    >
                        <span>View Data</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-[10px] font-bold">
                            {{ $txCount }}
                        </span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        wire:click="addTransaction({{ $category->id }})"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-700/60 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400 text-gray-700 dark:text-gray-300 transition"
                        title="Add transaction inside this category"
                    >
                        <x-icon name="plus" class="w-3 h-3" />
                        <span>Add Tx</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 mx-auto flex items-center justify-center mb-3">
                    <x-icon name="tag" class="w-6 h-6" />
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">No categories found</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">Create a custom category or clear your search filters to view your categories.</p>
                <button
                    wire:click="openNewCategoryModal"
                    class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-500 transition shadow-sm"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Create Category</span>
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal 1: Create / Edit Category Modal -->
    @if($showCategoryModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity" wire:click="$set('showCategoryModal', false)"></div>

                <div class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700/80">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $editingCategoryId ? 'Edit Category' : 'Create New Category' }}
                        </h3>
                        <button wire:click="$set('showCategoryModal', false)" class="text-gray-400 hover:text-gray-500 p-1">
                            <x-icon name="plus" class="w-5 h-5 rotate-45" />
                        </button>
                    </div>

                    <form wire:submit="saveCategory" class="mt-4 space-y-4">
                        <!-- Category Name -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Category Name</label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="e.g. Gym & Fitness, Pet Care, Freelance..."
                                class="w-full text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/60 text-gray-900 dark:text-white px-3.5 py-2.5 focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Category Type -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition {{ $type === 'expense' ? 'border-rose-500 bg-rose-50/50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-300 font-bold' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400' }}">
                                    <input type="radio" wire:model.live="type" value="expense" class="text-rose-600 focus:ring-rose-500" />
                                    <span>Expense Category</span>
                                </label>
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition {{ $type === 'income' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300 font-bold' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400' }}">
                                    <input type="radio" wire:model.live="type" value="income" class="text-emerald-600 focus:ring-emerald-500" />
                                    <span>Income Category</span>
                                </label>
                            </div>
                            @error('type') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Icon Selection -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Choose Icon</label>
                            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2 p-2 rounded-xl bg-gray-50 dark:bg-gray-700/30 max-h-36 overflow-y-auto">
                                @php
                                    $availableIcons = [
                                        'shopping-cart', 'utensils', 'home', 'car', 'bolt', 'film',
                                        'heart-pulse', 'receipt', 'briefcase', 'laptop', 'gift',
                                        'calendar', 'wallet', 'credit-card', 'banknotes', 'bank',
                                        'device-phone-mobile', 'trending-up', 'tag', 'arrow-path'
                                    ];
                                @endphp
                                @foreach($availableIcons as $ic)
                                    <button
                                        type="button"
                                        wire:click="$set('icon', '{{ $ic }}')"
                                        class="p-2 rounded-lg flex items-center justify-center transition {{ $icon === $ic ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                                        title="{{ $ic }}"
                                    >
                                        <x-icon :name="$ic" class="w-4 h-4" />
                                    </button>
                                @endforeach
                            </div>
                            @error('icon') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Color Palette Picker -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Accent Color</label>
                            <div class="flex items-center gap-2 flex-wrap">
                                @php
                                    $colorPresets = [
                                        '#6366f1', '#f97316', '#10b981', '#0ea5e9', '#ec4899',
                                        '#8b5cf6', '#ef4444', '#eab308', '#14b8a6', '#06b6d4',
                                        '#f43f5e', '#64748b'
                                    ];
                                @endphp
                                @foreach($colorPresets as $cp)
                                    <button
                                        type="button"
                                        wire:click="$set('color_hex', '{{ $cp }}')"
                                        class="w-7 h-7 rounded-full transition-transform {{ $color_hex === $cp ? 'ring-2 ring-offset-2 ring-indigo-500 scale-110' : 'hover:scale-105' }}"
                                        style="background-color: {{ $cp }}"
                                    ></button>
                                @endforeach
                                <input
                                    type="color"
                                    wire:model.live="color_hex"
                                    class="w-7 h-7 rounded-full cursor-pointer border-0 p-0"
                                    title="Custom Color"
                                />
                            </div>
                            @error('color_hex') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Live Card Preview -->
                        <div class="p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/20">
                            <span class="text-[10px] uppercase font-bold text-gray-400 block mb-1.5">Live Preview</span>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl flex items-center justify-center text-white shrink-0"
                                    style="background-color: {{ $color_hex }}"
                                >
                                    <x-icon :name="$icon" class="w-4 h-4" />
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $name ?: 'Category Name' }}</p>
                                    <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded {{ $type === 'expense' ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' }}">
                                        {{ ucfirst($type) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-2.5">
                            <button
                                type="button"
                                wire:click="$set('showCategoryModal', false)"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-500/20 transition"
                            >
                                {{ $editingCategoryId ? 'Save Changes' : 'Create Category' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal 2: View Category Data Modal -->
    @if($showDataModal && $viewingCategory)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity" wire:click="closeDataModal"></div>

                <div class="relative inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700/80">
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-sm"
                                style="background-color: {{ $viewingCategory->color_hex }}"
                            >
                                <x-icon :name="$viewingCategory->icon" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $viewingCategory->name }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Transactions and records stored inside this category
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeDataModal" class="text-gray-400 hover:text-gray-500 p-1">
                            <x-icon name="plus" class="w-5 h-5 rotate-45" />
                        </button>
                    </div>

                    <!-- Category Data Highlights -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 my-4">
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30">
                            <span class="text-[10px] font-semibold text-gray-400 block uppercase">Category Type</span>
                            <span class="text-sm font-bold {{ $viewingCategory->type === 'expense' ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ ucfirst($viewingCategory->type) }}
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30">
                            <span class="text-[10px] font-semibold text-gray-400 block uppercase">Transactions Count</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $categoryTransactions->count() }} records
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 col-span-2 sm:col-span-1">
                            <span class="text-[10px] font-semibold text-gray-400 block uppercase">Recent Sum</span>
                            <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                {{ auth()->user()->formatMoney($categoryTransactions->sum('amount')) }}
                            </span>
                        </div>
                    </div>

                    <!-- Transactions Table inside this category -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Recorded Transactions
                            </h4>
                            <a
                                href="{{ route('transactions', ['category_id' => $viewingCategory->id]) }}"
                                wire:navigate
                                class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
                            >
                                Open in Full Ledger &rarr;
                            </a>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700/60 max-h-64 overflow-y-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 font-semibold border-b border-gray-100 dark:border-gray-700/60">
                                    <tr>
                                        <th class="py-2.5 px-3">Date</th>
                                        <th class="py-2.5 px-3">Payee / Merchant</th>
                                        <th class="py-2.5 px-3">Wallet</th>
                                        <th class="py-2.5 px-3 text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/40">
                                    @forelse($categoryTransactions as $tx)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20">
                                            <td class="py-2 px-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($tx->transaction_date)->format('M d, Y') }}
                                            </td>
                                            <td class="py-2 px-3 font-semibold text-gray-900 dark:text-white">
                                                {{ $tx->payee_merchant ?: 'General ' . ucfirst($tx->type) }}
                                            </td>
                                            <td class="py-2 px-3 text-gray-500 dark:text-gray-400">
                                                {{ $tx->wallet?->name ?? '—' }}
                                            </td>
                                            <td class="py-2 px-3 text-right font-bold {{ $tx->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                {{ $tx->type === 'income' ? '+' : '-' }}{{ auth()->user()->formatMoney($tx->amount) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-gray-400 dark:text-gray-500">
                                                No transactions recorded in this category yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="mt-5 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <button
                            type="button"
                            wire:click="addTransaction({{ $viewingCategory->id }})"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-sm transition"
                        >
                            <x-icon name="plus" class="w-3.5 h-3.5" />
                            <span>Add Transaction to {{ $viewingCategory->name }}</span>
                        </button>

                        <button
                            type="button"
                            wire:click="closeDataModal"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
