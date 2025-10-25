<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . "/../routes/web.php", api: __DIR__ . "/../routes/api.php", commands: __DIR__ . "/../routes/console.php", health: "/up")
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->prependToGroup("api", [\App\Http\Middleware\ForceJsonReponse::class]);
        $middleware->api(append: [\App\Http\Middleware\CheckDisabled::class, \App\Http\Middleware\TeamsPermission::class]);
        $middleware->alias([
            "abilities" => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            "ability" => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            "role" => \Spatie\Permission\Middleware\RoleMiddleware::class,
            "permission" => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            "role_or_permission" => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            "isActive" => \App\Http\Middleware\CheckDisabled::class,
            "SPAOnly" => \App\Http\Middleware\SPAOnly::class,
            "ClientCheck" => \App\Http\Middleware\ApiClient::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
