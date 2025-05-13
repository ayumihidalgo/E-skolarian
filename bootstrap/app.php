<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectTo(
            guests: '/login',
            users: function ($request) {
                $user = $request->user();

                return match ($user->role) {
                    'super admin' => '/super-admin/dashboard',
                    'admin' => '/admin/dashboard',
                    'student' => '/student/dashboard',
                    default => '/login',
                };
            }
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
