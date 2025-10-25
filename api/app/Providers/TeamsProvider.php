<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\User;

use App\Observers\UserTeamObserver;
use App\Observers\TeamObserver;

class TeamsProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        if (config("permission.teams")) {
            User::observe(UserTeamObserver::class);
            app(config("mitd.permission.teams_provider"))::observe(TeamObserver::class);
        }
    }
}
