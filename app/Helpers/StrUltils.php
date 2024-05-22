<?php

namespace App\Helpers;

if (!function_exists('trans_format')) {
    function trans_format($key)
    {
        return ucfirst(strtolower(__($key)));
    }
}
