<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'limit_amount',
        'month_year',
        'alert_threshold_percentage',
    ];

    protected function casts(): array
    {
        return [
            'limit_amount' => 'decimal:2',
            'alert_threshold_percentage' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Calculate how much has been spent for this category in this budget's month.
     */
    public function getSpentAmountAttribute(): float
    {
        return (float) Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->where('transaction_date', 'like', "{$this->month_year}%")
            ->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->limit_amount - $this->spent_amount);
    }

    public function getPercentageUsedAttribute(): float
    {
        if ($this->limit_amount <= 0) {
            return 0.0;
        }

        return round(($this->spent_amount / (float) $this->limit_amount) * 100, 1);
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->spent_amount > (float) $this->limit_amount;
    }

    public function getIsNearLimitAttribute(): bool
    {
        $threshold = (float) ($this->alert_threshold_percentage ?: 80.00);
        return $this->percentage_used >= $threshold && ! $this->is_over_budget;
    }
}
