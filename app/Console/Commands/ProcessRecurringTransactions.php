<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-recurring-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and auto-post due recurring bills, subscriptions, and regular income';

    /**
     * Execute the console command.
     */
    public function handle(TransactionService $transactionService): int
    {
        $dueItems = RecurringTransaction::with(['user', 'wallet', 'category'])
            ->where('is_active', true)
            ->where('auto_post', true)
            ->where('next_due_date', '<=', Carbon::today()->toDateString())
            ->get();

        if ($dueItems->isEmpty()) {
            $this->info('No due recurring transactions to process.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($dueItems as $item) {
            $transactionService->create($item->user, [
                'wallet_id' => $item->wallet_id,
                'category_id' => $item->category_id,
                'type' => $item->type,
                'amount' => $item->amount,
                'transaction_date' => $item->next_due_date,
                'payee_merchant' => $item->name,
                'notes' => 'Auto-posted recurring: ' . ($item->notes ?? $item->name),
                'tags' => ['recurring', 'auto-posted'],
            ]);

            $nextDate = $item->getNextComputedDueDate();

            $item->update([
                'last_posted_at' => Carbon::now(),
                'next_due_date' => $nextDate->toDateString(),
            ]);

            $count++;
            $this->info("Processed [{$item->name}] of {$item->user->formatMoney($item->amount)} for {$item->user->name}. Next due: {$nextDate->toDateString()}");
        }

        $this->info("Successfully processed {$count} recurring transaction(s).");
        return self::SUCCESS;
    }
}
