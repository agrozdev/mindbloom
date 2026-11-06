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
            // One-click unsubscribe from Gmail/Outlook (List-Unsubscribe-Post) can't send a CSRF token.
            'otpisvane/*',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\CaptureReaderToken::class,
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

            // Post slugs are unique site-wide, so /blog/<old-category>/<slug> can be
            // resolved even when the category was renamed without a redirect
            // (e.g. roden-ot-oganya-11-15g -> 11-16g left three indexed URLs as 404s).
            if (preg_match('#^/blog/[^/]+/([^/]+)$#', $path, $m)) {
                $post = \App\Models\Post::query()->with('category')->where('slug', $m[1])->first();

                if ($post && $post->category) {
                    $target = route('blog.show', [$post->category, $post], false);

                    if ($target !== $path) {
                        return redirect($target, 301);
                    }
                }

                // The post's own slug changed too: use the redirect recorded for
                // that old slug under whichever category it was saved with.
                if (! $post) {
                    $byOldSlug = \App\Models\Redirect::query()
                        ->where('from_path', 'like', '/blog/%/' . str_replace(['%', '_'], ['\\%', '\\_'], $m[1]))
                        ->first();

                    if ($byOldSlug) {
                        $byOldSlug->increment('hits');

                        return redirect($byOldSlug->to_path, 301);
                    }
                }
            }

            return null;
        });
    })->create();
