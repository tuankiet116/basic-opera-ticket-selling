<?php

namespace App\Http\Services\Admin;

class AuthService
{

    public function isLogin() {
        return auth() ? true : false;
    }

    public function login(array $credentials)
    {
        dd($credentials);
        $isLogin = auth()->attempt($credentials, data_get($credentials, "remember_me", false));
        if ($isLogin) {
            return true;
        }
        return false;
    }
}
