<?php

namespace App\Observers;

use App\Events\UserCreated;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        UserCreated::broadcast($user);
    }
}
