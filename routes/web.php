<?php

use App\Http\Controllers\Admin\ExportController;
use Illuminate\Support\Facades\Route;

Route::prefix("admin/export")->group(function() {
    Route::get("aggregate/{event}", [ExportController::class, "exportReport"]);
})->middleware("auth:sanctum");

Route::fallback(function () {
    return view('index');
})->where("any", '^(?!api).*$');
