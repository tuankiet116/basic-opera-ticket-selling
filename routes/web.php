<?php

use App\Http\Controllers\Admin\FileController;
use App\Http\Middleware\GzipMiddleware;
use Illuminate\Support\Facades\Route;

Route::get("admin/files/download/{fileName}", [FileController::class, "download"])
    ->middleware("auth:sanctum");

Route::middleware(GzipMiddleware::class)->get("admin/{any?}", function () {
    return view('admin');
})->where("any", '^(?!api).*$');

Route::fallback(function () {
    return view('index');
});
