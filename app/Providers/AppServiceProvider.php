<?php

namespace App\Providers;

use App\Services\MobileCommons;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MobileCommons::class, function () {
            return new MobileCommons([
                'username' => env('MOBILE_COMMONS_USERNAME'),
                'password' => env('MOBILE_COMMONS_PASSWORD'),
            ]);
        });
    }
}
