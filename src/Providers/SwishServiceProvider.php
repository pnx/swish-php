<?php

namespace Olssonm\Swish\Providers;

use Illuminate\Support\ServiceProvider;
use Olssonm\Swish\Client;

class SwishServiceProvider extends ServiceProvider
{
    /**
     * Path to config-file
     *
     * @var string
     */
    protected $config;

    /**
     * Constructor
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->config = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';

        parent::__construct($app);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        //
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Publishing of configuration
        $this->publishes([
            $this->config => config_path('swish.php'),
        ]);

        // If the user doesn't set their own config, load default
        $this->mergeConfigFrom(
            $this->config,
            'swish'
        );

        $this->app->singleton('swish', function () {
            $certificates = config('swish.certificates');
            return new Client($certificates, config('swish.endpoint'));
        });

        $this->app->bind(\Olssonm\Swish\Client::class, 'swish');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function provides()
    {
        return ['swish'];
    }
}
