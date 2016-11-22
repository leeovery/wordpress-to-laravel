<?php

namespace LeeOvery\WordpressToLaravel;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Serializer\ArraySerializer;
use LeeOvery\WordpressToLaravel\Commands\Importer;

class WordpressToLaravelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // use this if your package needs a config file
        $this->publishes([
            $this->configPath() => config_path('wp-to-laravel.php'),
        ], 'config');

        // use the vendor configuration file as fallback
        $this->mergeConfigFrom($this->configPath(), 'wp-to-laravel');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../migrations');
            $this->loadMigrationsFrom(base_path() . '/vendor/cartalyst/tags/resources/migrations');
        }

        $this->bootCommands();
    }

    /**
     * @return string
     */
    protected function configPath()
    {
        return __DIR__ . '/../config/config.php';
    }

    protected function bootCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Importer::class,
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('wp-to-laravel', function ($app) {
            $manager = new FractalManager;
            $manager->setSerializer(new ArraySerializer());

            return new WordpressToLaravel(
                $manager,
                new GuzzleClient,
                $app->make('config')->get('wp-to-laravel')
            );
        });

        $this->app->alias('wp-to-laravel', WordpressToLaravel::class);
    }

    public function provides()
    {
        return ['wp-to-laravel', WordpressToLaravel::class];
    }
}