<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EventController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->prefix("/admin")->group(function () {
    Route::get('/is-logged-in', [AuthController::class, "isLoggedIn"]);
    Route::post('/logout', [AuthController::class, "logout"]);
    Route::prefix("/event")->group(function () {
        Route::post("/create", [EventController::class, "create"]);
        Route::get("/{eventId}", [EventController::class, "edit"]);
        Route::put("/update/{eventId}", [EventController::class, "update"]);
        Route::get("/list", [EventController::class, "listAll"]);
    });
});
