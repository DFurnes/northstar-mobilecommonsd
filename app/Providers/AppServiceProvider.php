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

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /** @var \DoSomething\StatHat\Client $stathat */
        $stathat = app('stathat');

        app('queue')->failing(function () use ($stathat) {
            $stathat->ezCount('job failed');
        });
    }
}
