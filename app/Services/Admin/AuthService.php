<?php

namespace App\Services\Admin;

class AuthService
{
    public function login(array $credentials)
    {
        $isLogin = auth()->attempt([
            "email" => $credentials["email"],
            "password" => $credentials["password"]
        ], data_get($credentials, "remember_me", false));
        if ($isLogin) {
            return true;
        }
        return false;
    }
}
