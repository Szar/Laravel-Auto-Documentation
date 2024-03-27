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


        // SeacoastBank\AutoDocumentation\Providers\AutoDocumentationProvider::class,

       /* $this->app['apidocumentation'] = $this->app->share(function($app)
        {
            return new AutoDocumentation(
                new phpDocumentor\Reflection()
            );
        });*/

        $this->app->router->group(['namespace' => 'SeacoastBank\AutoDocumentation\App\Http\Controllers'],
            function(){
                require __DIR__.'/routes/web.php';
            }
        );
        $this->loadViewsFrom(__DIR__.'/views', 'SeacoastBank\AutoDocumentation');
        $this->publishes(
            [
                __DIR__.'/views' => base_path('resources/views/vendor/seacoastbank/autodocumentation'),
            ]
        );
        $this->publishes([
            __DIR__.'/config' => config_path('vendor/seacoastbank/autodocumentation'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Generate::class
            ]);
        }

        //$kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        //$kernel->pushMiddleware('Yk\LaravelPackageExample\App\Http\Middleware\MiddlewareExample');




        /*if ($this->app->runningInConsole()) {
            $this->commands([
                Generate::class
            ]);
        }*/
    }
}