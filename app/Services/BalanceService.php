<?php

namespace App\Services;

use App\Enum\Currency;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BalanceService
{
    public function userBalances(User $user): Collection
    {
        return $user->balances;
    }

    public function getBalance(User $user, Currency $currency, bool $lock = false): Balance
    {
        $balance = $user->balances()
            ->when($lock, fn (Builder $q) => $q->lockForUpdate())
            ->firstWhere(compact('currency'));

        if (! $balance) {
            $balance = new Balance(compact('currency'));
            $user->balances()->save($balance);
        } else {
            $lock = false; // for the next step we need disable lock if balance already locked
        }

        if ($lock) {
            $balance = $user->balances()
                ->lockForUpdate()
                ->firstWhere(compact('currency'));
        }

        return $balance;
    }

    public function updateBalance(Balance $balance, float $amount): bool
    {
        return $balance->update(compact('amount'));
    }

    public function increaseBalance(Balance $balance, float $amount): bool
    {
        return $this->updateBalance($balance, $balance->amount + $amount);
    }

    public function decreaseBalance(Balance $balance, float $amount): bool
    {
        return $this->updateBalance($balance, $balance->amount - $amount);
    }

    public function increaseUserBalance(User $user, Currency $currency, float $amount): bool
    {
        $balance = $this->getBalance($user, $currency);

        return $this->updateBalance($balance, $balance->amount + $amount);
    }

    public function decreaseUserBalance(User $user, Currency $currency, float $amount): bool
    {
        $balance = $this->getBalance($user, $currency);

        return $this->updateBalance($balance, $balance->amount - $amount);
    }

    public function createUserBalances(User $user): int
    {
        $existingCurrencies = $user->balances()->pluck('currency')->map->value->all();
        $currencies = array_map(fn (Currency $currency) => $currency->value, Currency::cases());
        $needleCurrencies = array_diff($currencies, $existingCurrencies);

        foreach ($needleCurrencies as $needleCurrency) {
            $this->getBalance($user, Currency::from($needleCurrency));
        }

        return count($needleCurrencies);
    }
}
