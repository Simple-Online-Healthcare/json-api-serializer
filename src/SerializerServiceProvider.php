<?php

namespace SimpleOnlineHealthcare\Doctrine;

use Illuminate\Support\ServiceProvider;

class SerializerServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/serializer.php' => config_path('serializer.php'),
        ], 'config');
    }

    public function register()
    {
        //
    }
}