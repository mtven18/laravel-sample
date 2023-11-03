<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Str;

class SanctumTokenAuth extends AuthService
{
    public function login(string $username, string $password): bool|LoginDataDto
    {
        $user = $this->findUser($username);

        if (! $user || ! $this->checkPassword($user, $password)) {
            return false;
        }

        return new LoginDataDto([
            'user' => $user,
            'token' => $user->createToken(Str::uuid())->plainTextToken,
        ]);
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function user(): User
    {
        /** @var User $user */
        $user = $this->getGuard()->user();

        return $user;
    }
}
