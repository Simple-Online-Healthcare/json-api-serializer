<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use Illuminate\Support\ServiceProvider;
use SimpleOnlineHealthcare\JsonApi\Registries\ConfigurationRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\ResourceTypeRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\TransformerRegistry;

class SerializerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/json-api-serializer.php' => config_path('json-api-serializer.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton(ResourceTypeRegistry::class, function () {
            return new ResourceTypeRegistry(config('jsonapi.resource_type_mapping', []));
        });

        $this->app->singleton(TransformerRegistry::class, function () {
            return new TransformerRegistry(config('jsonapi.transformer_mapping', []));
        });
    }
}
