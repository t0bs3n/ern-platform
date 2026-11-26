<?php

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
        // Guests who open a member page go to sign-in; members who open sign-in go to the member area.
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo('/members');

        // The admin area (R0-08): signed in, and holding a role beyond member.
        // Every tab adds its own can:<gate> on top.
        $middleware->group('admin', ['auth', 'can:open-admin-area']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
