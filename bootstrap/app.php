<?php

use Dotenv\Exception\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Spatie\Permission\Middlewares\RoleMiddleware;
use Spatie\Permission\Middlewares\PermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {

            if ($request->is('api/*')) {

                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 404);
                }

                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Método no permitido en esta ruta.'
                    ], 405);
                }
            }
        });


        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return response()->json([
                'responseMessage' => 'No tienes los permisos necesarios para realizar esta acción.',
                'responseStatus'  => 403,
            ]);
        });
    })->create();
