<?php

use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FileController;
use Illuminate\Support\Facades\Route;

Route::get("admin/files/download/{fileName}", [FileController::class, "download"])->middleware("auth:sanctum");

// Route::get("/admin/*", function () {
    
// });
Route::fallback(function () {
    return view('index');
})->where("any", '^(?!api).*$');
