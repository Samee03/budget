<?php

use App\Http\Middleware\AlwaysAcceptJson;
use App\Support\ApiResponseFacade;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(AlwaysAcceptJson::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e) {
            return ApiResponseFacade::error(
                $e->getMessage(),
                422,
                $e->errors()
            );
        })->renderable(function (NotFoundHttpException $e) {
            $request = request();

            if ($request->wantsJson()) {
                return ApiResponseFacade::error('Object not found', $e->getStatusCode());
            }
        })->renderable(function (ThrottleRequestsException $e) {
            return ApiResponseFacade::error($e->getMessage(), $e->getStatusCode());
        });
    })->create();
