<?php

use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\PreviewMail;
use App\Http\Middleware\GzipMiddleware;
use App\Models\EventModel;
use Illuminate\Support\Facades\Route;

Route::get("admin/files/download/{fileName}", [FileController::class, "download"])
    ->middleware("auth:sanctum");

Route::middleware("auth:sanctum")->prefix("admin/preview")->group(function () {
    Route::get("asking-payment", [PreviewMail::class, "previewAskingPayment"]);

    Route::get("payment-success", [PreviewMail::class, "previewPaymentSuccess"]);
});

Route::middleware(GzipMiddleware::class)->get("admin/{any?}", function () {
    return view('admin');
})->where("any", '^(?!api).*$');

Route::fallback(function () {
    return view('index');
});
