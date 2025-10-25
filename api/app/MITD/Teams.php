<?php

namespace App\MITD;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthenticationController;

class Teams {
    public static function routes(string|array $middlewares = []) {
        self::authRoutes($middlewares);
        self::userRoutes($middlewares);
    }

    public static function userRoutes(string|array $middlewares = []) {
        if (config("permission.teams")) {
            Route::prefix("user")
                ->group(function () {
                    Route::put("team/{user}", [UsersController::class, "changeTeam"])->name("users.team.switch");
                })
                ->middleware($middlewares);
        }
    }

    public static function authRoutes(string|array $middlewares = []) {
        if (config("permission.teams")) {
            Route::prefix("auth")
                ->group(function () {
                    Route::get("teams", [AuthenticationController::class, "getTeams"])->name("auth.teams");
                    route::put("team", [AuthenticationController::class, "switchTeam"])->name("auth.team.switch");
                })
                ->middleware($middlewares);
        }
    }
}
