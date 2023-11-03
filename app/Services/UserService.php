<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function register(array $data): User
    {
        /** @var User $user */
        $user = User::query()->create($data);

        return $user;
    }
}
