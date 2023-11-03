<?php

namespace App\Services\Auth;

use App\Models\User;
use Spatie\DataTransferObject\DataTransferObject;

class LoginDataDto extends DataTransferObject
{
    public User $user;
    public ?string $token;
}
