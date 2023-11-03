<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function jsonMessageResponse(string $message, int $status = BaseResponse::HTTP_OK): JsonResponse
    {
        return response()->json(compact('message'), $status);
    }

    protected function noContentResponse(): Response
    {
        return response()->noContent();
    }
}
