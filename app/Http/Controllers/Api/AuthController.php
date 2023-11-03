<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    public function login(LoginRequest $request): UserResource|JsonResponse
    {
        $result = $this->authService->login($request->username, $request->password);

        if ($result === false) {
            return $this->jsonMessageResponse(
                __('auth.credentials'),
                BaseResponse::HTTP_UNAUTHORIZED
            );
        }

        return (new UserResource($result->user))->additional([
            'token' => $result->token,
        ]);
    }

    public function logout(Request $request): Response
    {
        $this->authService->logout($request->user());

        return $this->noContentResponse();
    }

    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
