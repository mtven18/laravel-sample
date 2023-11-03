<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Hash;

abstract class AuthService
{
    abstract public function login(string $username, string $password): bool|LoginDataDto;

    abstract public function logout(User $user): void;

    abstract public function user(): User;

    final protected function findUser(string $username): ?User
    {
        return User::query()->whereUsername($username)->first();
    }

    protected function checkPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    final protected function getGuard(?string $guard = null): Guard|StatefulGuard|Factory
    {
        return auth($guard);
    }
}
