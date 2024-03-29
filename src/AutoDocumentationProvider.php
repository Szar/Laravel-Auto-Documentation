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
        $this->app->router->group(['namespace' => 'SeacoastBank\AutoDocumentation\App\Http\Controllers'],
            function(){
                require __DIR__.'/routes/web.php';
            }
        );
        //$this->publishes([ __DIR__.'/config' => config_path('vendor/seacoastbank/autodocumentation') ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Generate::class
            ]);
        }
    }
}