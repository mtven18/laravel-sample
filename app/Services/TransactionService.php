<?php

namespace App\Services;

use App\Enum\Currency;
use App\Enum\TransactionStatus;
use App\Exceptions\ForbiddenException;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Concerns\ListBaseParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Throwable;

class TransactionService
{
    use ListBaseParams;

    public function __construct(
        private readonly BalanceService $balanceService,
        private readonly ConnectionInterface $db,
    ) {
    }

    public function userTransactions(User $user, array $params = []): LengthAwarePaginator|Collection
    {
        $params = $this->validateListParams($params);

        $query = Transaction::query()
            ->userTransactions($user->id);

        return $this->getResult($query, $params['limit']);
    }

    /**
     * @throws Throwable
     */
    public function sendTransaction(User $fromUser, string $to, float $amount, Currency $currency): Transaction
    {
        $toUser = User::query()
            ->whereUsername($to)
            ->whereNot('id', $fromUser->id)
            ->firstOr(fn () => throw new ForbiddenException(__('messages.invalid_transaction_recipient')));

        $this->db->beginTransaction();

        try {
            $transaction = $this->processTransaction($fromUser, $toUser, $amount, $currency);
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        $this->db->commit();

        return $transaction;
    }

    private function processTransaction(User $fromUser, User $toUser, float $amount, Currency $currency): Transaction
    {
        $fromBalance = $this->balanceService->getBalance($fromUser, $currency, true);
        $toBalance = $this->balanceService->getBalance($toUser, $currency, true);

        $transaction = new Transaction([
            'currency' => $currency,
            'amount' => $amount,
            'status' => TransactionStatus::PENDING,
        ]);
        $transaction->fromUser()->associate($fromUser);
        $transaction->toUser()->associate($toUser);
        $transaction->save();

        if ($fromBalance->amount < $amount) {
            $transaction->update([
                'failed_reason' => __('messages.balance_transaction_error'),
                'status' => TransactionStatus::FAILED,
            ]);

            return $transaction;
        }

        $this->balanceService->decreaseBalance($fromBalance, $amount);
        $this->balanceService->increaseBalance($toBalance, $amount);

        $transaction->update([
            'status' => TransactionStatus::SUCCESS,
        ]);

        return $transaction;
    }
}
