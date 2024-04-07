<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EventController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->prefix("/admin")->group(function () {
    Route::prefix("/event")->group(function () {
        Route::get("/list", [EventController::class, "listAll"]);
    });
});
