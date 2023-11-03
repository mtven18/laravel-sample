<?php

namespace App\Observers;

use App\Enum\TransactionStatus;
use App\Models\Transaction;

class TransactionObserver
{
    public function updating(Transaction $transaction): void
    {
        if ($transaction->isDirty('status') &&
            in_array($transaction->status, TransactionStatus::completedStatuses())) {
            $transaction->completed_at = now();
        }
    }
}
