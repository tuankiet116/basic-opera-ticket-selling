<?php

namespace App\Http\Services\Admin;

class AuthService
{
    public function login(array $credentials)
    {
        $isLogin = auth()->attempt($credentials);
        if ($isLogin) {
            return true;
        }
        return false;
    }
}
