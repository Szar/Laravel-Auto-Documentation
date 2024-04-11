<?php

namespace SeacoastBank\AutoDocumentation;

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
        if (\File::exists(__DIR__.'/../vendor/autoload.php'))
        {
            include __DIR__.'/../vendor/autoload.php';
        }
        if ($this->app->runningInConsole()) {
            $this->commands([
                Generate::class
            ]);
        }
    }
}