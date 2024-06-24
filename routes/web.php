<?php

use App\Http\Controllers\Admin\FileController;
use Illuminate\Support\Facades\Route;

Route::get("admin/files/download/{fileName}", [FileController::class, "download"])->middleware("auth:sanctum");

Route::get("admin/{any?}", function () {
    return view('admin');
})->where("any", '^(?!api).*$');;

Route::fallback(function () {
    return view('index');
});
