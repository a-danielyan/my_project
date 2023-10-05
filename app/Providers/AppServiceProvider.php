<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.debug') === true) {
            DB::listen(function ($query) {
                $monolog = new Logger('log');
                $monolog->pushHandler(
                    new StreamHandler(storage_path('logs/query.log')),
                );
                $monolog->info('Query', ['sql' => $query->sql, 'bindings' => $query->bindings, 'time' => $query->time]);
            });
        }
    }
}
