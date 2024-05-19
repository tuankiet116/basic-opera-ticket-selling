<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\SeatController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\EventController as ClientEventController;
use App\Http\Controllers\Client\TicketController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->prefix("/admin")->group(function () {
    Route::get('/is-logged-in', [AuthController::class, "isLoggedIn"]);
    Route::post('/logout', [AuthController::class, "logout"]);
    Route::prefix("/event")->group(function () {
        Route::post("/create", [EventController::class, "create"]);
        Route::get("/edit/{eventId}", [EventController::class, "edit"]);
        Route::put("/update/{eventId}", [EventController::class, "update"]);
        Route::get("/list", [EventController::class, "listAll"]);
        Route::put("/status/{eventId}", [EventController::class, "updateStatus"]);
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
    });
});

Route::prefix("/event")->group(function () {
    Route::get("/list", [ClientEventController::class, "list"]);
    Route::get("/info/{eventId}", [ClientEventController::class, "getEvent"]);
    Route::get("/bookings/{eventId}", [ClientEventController::class, "getBookings"]);
});

Route::get("/ticket-classes/{eventId}", [TicketController::class, "getTicketClasses"]);
Route::post("/booking", [BookingController::class, "booking"]);
