<?php

use App\Console\Commands\CheckPendingBooking;
use App\Console\Commands\RemoveBookingTemporary;
use App\Console\Commands\RemoveClientInvalid;
use App\Http\Middleware\SetLocale;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->append(SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withSchedule(function (Schedule $schedule) {
        $schedule->command(CheckPendingBooking::class)->everyMinute();
        $schedule->command(RemoveClientInvalid::class)->everyMinute();
        $schedule->command(RemoveBookingTemporary::class)->everyMinute();
    })->create();
