<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\RegisterRequest;
use App\Http\Resources\Balance\BalanceResource;
use App\Http\Resources\User\UserResource;
use App\Services\BalanceService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly BalanceService $balanceService,
    ) {
    }

    public function register(RegisterRequest $request): UserResource
    {
        $user = $this->userService->register($request->validated());

        return new UserResource($user);
    }

    public function balances(Request $request): AnonymousResourceCollection
    {
        return BalanceResource::collection(
            $this->balanceService->userBalances($request->user())
        );
    }
}
