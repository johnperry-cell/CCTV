<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        if ($this->app->environment("local")) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        // URL::forceRootUrl(config("app.url"));
        URL::useOrigin(config("app.url"));
        if (Str::contains(config("app.url"), "https://")) {
            URL::forceScheme("https");
        }

        Gate::before(function ($user, $ability) {
            return $user->hasRole(config("mitd.superman")) ? true : null;
        });

        Password::defaults(function () {
            $rules = Password::min(config("mitd.password.rules.min"));

            if (config("mitd.password.rules.max") !== null) {
                $rules->max(config("mitd.password.rules.max"));
            }

            if (config("mitd.password.rules.letters")) {
                $rules->letters();

                if (config("mitd.password.rules.mixedCase")) {
                    $rules->mixedCase();
                }
            }

            if (config("mitd.password.rules.numbers")) {
                $rules->numbers();
            }

            if (config("mitd.password.rules.symbols")) {
                $rules->symbols();
            }

            if (config("mitd.password.rules.uncompromised")) {
                $rules->uncompromised(config("mitd.password.rules.compromisedThreshold"));
            }

            return $rules;
        });
    }
}
