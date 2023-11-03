<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Services\BalanceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

readonly class CreateUserBalances implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private BalanceService $balanceService,
    ) {
    }

    public function handle(UserCreated $event): void
    {
        $this->balanceService->createUserBalances($event->user);
    }
}
