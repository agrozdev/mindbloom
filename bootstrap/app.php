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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'payments/mypos/notify',
            'payments/*/thank-you',
            'payments/*/cancelled',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Serve 301s for URLs whose slug changed (see `content:reslug` + the
        // `redirects` table). Runs only when a request is about to 404, so it
        // costs nothing on normal traffic, and covers both "no route matched"
        // and "route matched but model binding failed".
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e,
            \Illuminate\Http\Request $request,
        ) {
            $path = '/' . trim($request->path(), '/');
            $redirect = \App\Models\Redirect::query()->where('from_path', $path)->first();

            if ($redirect) {
                $redirect->increment('hits');

                return redirect($redirect->to_path, 301);
            }

            return null;
        });
    })->create();
