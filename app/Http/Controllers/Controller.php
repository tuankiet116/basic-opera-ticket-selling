<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function responseError(array $data = [], int $errorCode = HTTP_CODE["INTERNAL_SERVER"]): JsonResponse
    {
        return response()->json($data, $errorCode);
    }

    public function responseSuccess(array $data = []): JsonResponse
    {
        return response()->json($data);
    }
}
