<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'category_id',
        'name',
        'type',
        'amount',
        'frequency',
        'start_date',
        'next_due_date',
        'auto_post',
        'is_active',
        'last_posted_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'start_date' => 'date',
            'next_due_date' => 'date',
            'auto_post' => 'boolean',
            'is_active' => 'boolean',
            'last_posted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('next_due_date', '<=', Carbon::today()->toDateString());
    }

    public function getNextComputedDueDate(): Carbon
    {
        $current = Carbon::parse($this->next_due_date);

        return match ($this->frequency) {
            'daily' => $current->copy()->addDay(),
            'weekly' => $current->copy()->addWeek(),
            'biweekly' => $current->copy()->addWeeks(2),
            'monthly' => $current->copy()->addMonth(),
            'yearly' => $current->copy()->addYear(),
            default => $current->copy()->addMonth(),
        };
    }
}
