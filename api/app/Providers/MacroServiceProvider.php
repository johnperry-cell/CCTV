<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Builder;

use App\MITD\Paginates;

class MacroServiceProvider extends ServiceProvider {
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
        Blueprint::macro("disabler", function () {
            $this->timestamp(config("mitd.models.disabler_column"))->nullable();
        });

        Builder::macro("paginates", function (int $limit = 25, ?int $page = null) {
            return Paginates::paginates($this, $limit, $page);
        });
    }
}
