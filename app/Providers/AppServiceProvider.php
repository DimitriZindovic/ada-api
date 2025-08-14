<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Solution CORS pour production
        if (app()->environment('production')) {
            // Forcer les headers CORS sur toutes les réponses
            app('router')->middleware('api')->group(function () {
                app('router')->pattern('any', '.*');
                app('router')->options('{any}', function () {
                    return response('', 200, [
                        'Access-Control-Allow-Origin' => request()->header('Origin', '*'),
                        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
                        'Access-Control-Max-Age' => '86400'
                    ]);
                });
            });
        }
    }
}
