<?php
/*
    * @author/Developer: Patrick Mutwiri
    * Email: patwiri@gmail.com
    * @link/GIT: https://github.com/patricmutwiri
    * @link/Twitter: https://twitter.com/patric_mutwiri
    * Call: +254727542899
    * @link/Site: http://patric.xyz

    --------------------------------------------------------------
    |         *******       **      ************ ************         |
    |         *******       **      ************ ************         |
    |         ***  **     **  **         ***     ***      ***         |
    |         ***  **     **  **         ***     ***      ***         |
    |         *******    ********        ***     ***      ***         |
    |         *******    ********        ***     ***      ***         |
    |         ***       **      **       ***     ************         |
    |         ***       **      **       ***     ************         |
    --------------------------------------------------------------
*/
namespace Patricmutwiri\Pesapal;

use Illuminate\Support\ServiceProvider;

class PesapalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
        $this->publishes([
            __DIR__.'/config/pesapal.php' => config_path('pesapal.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Patricmutwiri\Pesapal\PesapalController');
        $this->loadViewsFrom(__DIR__.'/views', 'pesapal');
        $this->mergeConfigFrom(
        __DIR__.'/config/pesapal.php', 'pesapal'
        );
    }
}
