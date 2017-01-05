<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    use DatabaseMigrations;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();

        // Resolve the Dispatcher, to fix a Lumen bug where mock gets overwritten
        // the first time the class is resolved from the service container.
        // @see: https://github.com/laravel/lumen-framework/issues/207
        app('Illuminate\Contracts\Bus\Dispatcher');
    }

    /**
     * Mock a class, and register with the IoC container.
     *
     * @param $class String - Class name to mock
     * @return \Mockery\MockInterface
     */
    public function mock($class)
    {
        $mock = Mockery::mock($class);
        $this->app->instance($class, $mock);

        return $mock;
    }
}
