<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use Illuminate\Support\ServiceProvider;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;

class SerializerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/json-api-serializer.php' => config_path('json-api-serializer.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->app->singleton(JsonApi::class, function () {
            return new JsonApi(config('json-api-serializer.jsonapi.version', '1.0'));
        });

        $this->app->singleton(Registry::class, function () {
            return new Registry(
                config('json-api-serializer.jsonapi.resource_types', []),
                config('json-api-serializer.jsonapi.normalizers', []),
            );
        });
    }
}
