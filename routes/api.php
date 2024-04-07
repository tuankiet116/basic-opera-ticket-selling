<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->group([
    "prefix" => "/admin",
], function () {
    Route::group(["prefix" => "/events"], function () {
        Route::post("/create");
    });
});
