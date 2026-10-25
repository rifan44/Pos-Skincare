<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('backup:run-with-heartbeat')
            ->dailyAt('03:00')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping(30);
        $schedule->command('backup:clean')
            ->dailyAt('03:30')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping(30);
        $schedule->command('backup:monitor')
            ->dailyAt('04:00')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping(30);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
