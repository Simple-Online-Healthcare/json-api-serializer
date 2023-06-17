<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use Illuminate\Support\ServiceProvider;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Registries\IncludedEntityRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\ResourceTypeRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\TransformerRegistry;

class SerializerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/json-api-serializer.php' => config_path('json-api-serializer.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton(JsonApi::class, function () {
            return new JsonApi(config('json-api-serializer.jsonapi.version', '1.0'));
        });

        $this->app->singleton(ResourceTypeRegistry::class, function () {
            return new ResourceTypeRegistry(config('json-api-serializer.jsonapi.resource_type_mapping', []));
        });

        $this->app->singleton(TransformerRegistry::class, function () {
            return new TransformerRegistry(config('json-api-serializer.jsonapi.transformer_mapping', []));
        });

        $this->app->singleton(IncludedEntityRegistry::class, function () {
            return new IncludedEntityRegistry();
        });
    }
}
