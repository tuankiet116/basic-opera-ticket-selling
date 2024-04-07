<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Services\Admin\AuthService;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $loginRequest) {
        $credentials = $loginRequest->validated();
        $isLoginSuccess = $this->authService->login($credentials);
        if ($isLoginSuccess) return $this->responseSuccess();
        else return $this->responseError();
    }
}
