<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionService
{
    /**
     * Create a transaction and adjust associated wallet balances atomically.
     */
    public function create(User $user, array $data, ?UploadedFile $receipt = null): Transaction
    {
        return DB::transaction(function () use ($user, $data, $receipt) {
            $receiptPath = null;
            if ($receipt) {
                $receiptPath = $receipt->store('receipts', 'public');
            }

            $amount = (float) $data['amount'];
            $wallet = Wallet::where('user_id', $user->id)->findOrFail($data['wallet_id']);

            if ($data['type'] === 'expense') {
                $wallet->decrement('balance', $amount);
            } elseif ($data['type'] === 'income') {
                $wallet->increment('balance', $amount);
            } elseif ($data['type'] === 'transfer') {
                $toWallet = Wallet::where('user_id', $user->id)->findOrFail($data['to_wallet_id']);
                $wallet->decrement('balance', $amount);
                $toWallet->increment('balance', $amount);
            }

            return Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'to_wallet_id' => $data['to_wallet_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'type' => $data['type'],
                'amount' => $amount,
                'transaction_date' => $data['transaction_date'],
                'payee_merchant' => $data['payee_merchant'] ?? null,
                'notes' => $data['notes'] ?? null,
                'receipt_path' => $receiptPath,
                'tags' => $data['tags'] ?? [],
            ]);
        });
    }

    /**
     * Update an existing transaction and recalculate wallet balances.
     */
    public function update(Transaction $transaction, array $data, ?UploadedFile $newReceipt = null): Transaction
    {
        return DB::transaction(function () use ($transaction, $data, $newReceipt) {
            // 1. Revert previous wallet impact
            $oldAmount = (float) $transaction->amount;
            $oldWallet = $transaction->wallet;

            if ($transaction->type === 'expense') {
                $oldWallet->increment('balance', $oldAmount);
            } elseif ($transaction->type === 'income') {
                $oldWallet->decrement('balance', $oldAmount);
            } elseif ($transaction->type === 'transfer' && $transaction->toWallet) {
                $oldWallet->increment('balance', $oldAmount);
                $transaction->toWallet->decrement('balance', $oldAmount);
            }

            // 2. Handle receipt replacement
            $receiptPath = $transaction->receipt_path;
            if ($newReceipt) {
                if ($receiptPath) {
                    Storage::disk('public')->delete($receiptPath);
                }
                $receiptPath = $newReceipt->store('receipts', 'public');
            }

            // 3. Apply new wallet impact
            $newAmount = (float) $data['amount'];
            $newWallet = Wallet::where('user_id', $transaction->user_id)->findOrFail($data['wallet_id']);

            if ($data['type'] === 'expense') {
                $newWallet->decrement('balance', $newAmount);
            } elseif ($data['type'] === 'income') {
                $newWallet->increment('balance', $newAmount);
            } elseif ($data['type'] === 'transfer') {
                $newToWallet = Wallet::where('user_id', $transaction->user_id)->findOrFail($data['to_wallet_id']);
                $newWallet->decrement('balance', $newAmount);
                $newToWallet->increment('balance', $newAmount);
            }

            $transaction->update([
                'wallet_id' => $newWallet->id,
                'to_wallet_id' => $data['to_wallet_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'type' => $data['type'],
                'amount' => $newAmount,
                'transaction_date' => $data['transaction_date'],
                'payee_merchant' => $data['payee_merchant'] ?? null,
                'notes' => $data['notes'] ?? null,
                'receipt_path' => $receiptPath,
                'tags' => $data['tags'] ?? [],
            ]);

            return $transaction;
        });
    }

    /**
     * Delete transaction, revert wallet balance and remove receipt.
     */
    public function delete(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $amount = (float) $transaction->amount;
            $wallet = $transaction->wallet;

            if ($wallet) {
                if ($transaction->type === 'expense') {
                    $wallet->increment('balance', $amount);
                } elseif ($transaction->type === 'income') {
                    $wallet->decrement('balance', $amount);
                } elseif ($transaction->type === 'transfer' && $transaction->toWallet) {
                    $wallet->increment('balance', $amount);
                    $transaction->toWallet->decrement('balance', $amount);
                }
            }

            if ($transaction->receipt_path) {
                Storage::disk('public')->delete($transaction->receipt_path);
            }

            return $transaction->delete();
        });
    }
}
