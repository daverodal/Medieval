<?php

namespace Wargame\Medieval;

use Illuminate\Support\ServiceProvider;

class MedievalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Grunwald1410/all.css' => public_path('vendor/wargame/medieval/grunwald1410/css/all.css'),
            __DIR__.'/Grunwald1410/Images' => public_path('vendor/wargame/medieval/grunwald1410/images'),

        ], 'css');

        $this->loadViewsFrom(dirname(__DIR__), 'wargame');
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
