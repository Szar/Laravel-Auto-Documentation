<?php

namespace SeacoastBank\AutoDocumentation\Providers;

use Illuminate\Support\ServiceProvider;
use SeacoastBank\AutoDocumentation\Commands\Generate;

class AutoDocumentationProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // SeacoastBank\AutoDocumentation\Providers\AutoDocumentationProvider::class,
        $this->publishes([
            __DIR__ . '/Console/stubs/SomeServiceProvider.stub' =>
                app_path('Providers/SomeServiceProvider.php'),
        ], 'wire-provider');
        $this->publishes([
            __DIR__ . '/../config/apidocumentation.php' => $this->app->configPath('apidocumentation.php'),
        ], 'apidocumentation-config');
        $this->mergeConfigFrom(__DIR__ . '/../config/apidocumentation.php', 'apidocumentation');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'autodocumentation');
        if ($this->app->runningInConsole()) {
            $this->commands([
                Generate::class
            ]);
        }
    }
}