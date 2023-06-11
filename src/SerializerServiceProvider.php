<?php

namespace SimpleOnlineHealthcare\JsonApi;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Registries\ConfigurationRegistry;

class SerializerServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/json-api-serializer.php' => config_path('json-api-serializer.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton(JsonApi::class, function (Application $application) {
            /** @var ConfigurationRegistry $configurationRegistry */
            $configurationRegistry = $application->make(ConfigurationRegistry::class);

            return new JsonApi($configurationRegistry->getJsonApiVersion());
        });
    }
}