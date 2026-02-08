<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $response = [
                    'message' => $e->getMessage(),
                    'errors' => [],
                ];

                $statusCode = 500;

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = 422;
                    $response['message'] = $e->getMessage();
                    foreach ($e->errors() as $field => $messages) {
                        foreach ($messages as $message) {
                            $response['errors'][] = [
                                $field => $message,
                            ];
                        }
                    }
                } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $statusCode = 401;
                    $response['message'] = 'Unauthenticated.';
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $statusCode = $e->getStatusCode();
                    $response['message'] = $e->getMessage();
                }

                if (empty($response['errors'])) {
                    $response['errors'][] = [
                        'message' => $response['message'],
                    ];
                }

                return response()->json($response, $statusCode);
            }

            return null;
        });
    })->create();
