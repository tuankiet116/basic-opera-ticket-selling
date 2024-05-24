<?php

namespace App\Exports;

use App\Models\User;

class UserExport
{
    public function __construct()
    {
    }

    public function collection()
    {
        return User::all();
    }
}
