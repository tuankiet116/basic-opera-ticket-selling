<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\SeatController;
use App\Http\Controllers\Admin\TicketClassController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\DiscountController as ClientDiscountController;
use App\Http\Controllers\Client\EventController as ClientEventController;
use App\Http\Controllers\Client\TicketController;
use App\Http\Middleware\GzipMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, "login"]);

Route::middleware(["auth:sanctum", GzipMiddleware::class])->prefix("/admin")->group(function () {
    Route::get('/is-logged-in', [AuthController::class, "isLoggedIn"]);
    Route::post('/logout', [AuthController::class, "logout"]);
    Route::prefix("/event")->group(function () {
        Route::post("/create", [EventController::class, "create"]);
        Route::get("/edit/{eventId}", [EventController::class, "edit"]);
        Route::put("/update/{eventId}", [EventController::class, "update"]);
        Route::get("/list", [EventController::class, "listAll"]);
        Route::put("/status/{eventId}", [EventController::class, "updateStatus"]);
        Route::delete("/delete/{eventId}", [EventController::class, "delete"]);
    });

    Route::prefix("/ticket-class/{eventModel}")->group(function () {
        Route::get("/list", [TicketClassController::class, "list"]);
    });

    Route::prefix("/discount")->group(function () {
        Route::get("/{eventModel}", [DiscountController::class, "index"]);
        Route::post("create", [DiscountController::class, "create"]);
        Route::put("update/{discountId}", [DiscountController::class, "update"]);
        Route::delete("delete/{discountModel}", [DiscountController::class, "delete"]);
    });

    Route::prefix("/seats")->group(function () {
        Route::get("/get-ticket-class/{eventId}", [SeatController::class, "getSeatTicketClass"]);
        Route::post("/set-ticket-class", [SeatController::class, "setSeatTicketClass"]);
        Route::post("/pre-booking", [SeatController::class, "preBookingTicket"])->name("pre-booking");
        Route::get("/booking/{eventId}", [SeatController::class, "getBookingStatus"]);
    });
    Route::prefix("/client")->group(function () {
        Route::get("/list", [ClientController::class, "getClients"]);
        Route::get("/special", [ClientController::class, "getClientsSpecial"]);
        Route::post("/create", [ClientController::class, "createClientSpecial"]);
        Route::put("/update/{clientId}", [ClientController::class, "updateClientSpecial"]);
        Route::get("/edit/{clientId}", [ClientController::class, "edit"]);
        Route::delete("/delete/{clientId}", [ClientController::class, "delete"]);
    });
    Route::prefix("/bookings")->group(function () {
        Route::get("/list/{eventId}", [AdminBookingController::class, "getBookings"]);
        Route::put("/accept", [AdminBookingController::class, "acceptBooking"]);
        Route::delete("/cancel/{eventId}/client/{clientId}", [AdminBookingController::class, "cancelBooking"]);
    });
    Route::prefix("/report")->group(function () {
        Route::post("/aggregate", [ExportController::class, "createReport"]);
    });
    Route::prefix("/files")->group(function () {
        Route::get("/list", [FileController::class, "index"]);
        Route::delete("/delete/{fileId}", [FileController::class, "delete"]);
    });
})->name("admin");

Route::prefix("/event")->middleware(GzipMiddleware::class)->group(function () {
    Route::get("/list", [ClientEventController::class, "list"]);
    Route::get("/info/{eventId}", [ClientEventController::class, "getEvent"]);
    Route::get("/bookings/{eventId}", [ClientEventController::class, "getBookings"]);
});

Route::middleware(GzipMiddleware::class)->group(function() {
    Route::get("/ticket-classes/{eventId}", [TicketController::class, "getTicketClasses"]);
    Route::post("/booking", [BookingController::class, "booking"]);
    Route::post("/temporary-booking", [BookingController::class, "temporaryBooking"]);
    Route::get("/temporary-booking", [BookingController::class, "getBookingsTemporary"]);
    Route::get("/temporary-token", [BookingController::class, "generateTeporaryToken"]);
    Route::prefix("/discount")->group(function() {
        Route::post("/apply", [ClientDiscountController::class, "apply"]);
    });
});
