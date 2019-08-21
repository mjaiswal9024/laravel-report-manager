<?php

namespace Drivezy\LaravelReportManager;

use Illuminate\Support\ServiceProvider;

class LaravelReportManagerServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return void
     */
    public function register () {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot () {
        //load the route defined in route file
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        //load migrations required for package
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}
