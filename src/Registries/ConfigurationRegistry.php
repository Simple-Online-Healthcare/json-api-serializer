<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Registries;

use Illuminate\Config\Repository;

class ConfigurationRegistry
{
    protected array $config;

    public function __construct(Repository $repository)
    {
        $this->config = $repository->get('json-api-serializer');
    }

    public function getJsonApiVersion(): string
    {
        return $this->getConfig('jsonapi.version');
    }

    public function getTransformerEntityMap(): array
    {
        return $this->getConfig('jsonapi.transformer_mapping');
    }

    public function getResourceTypeEntityMap(): array
    {
        return $this->getConfig('jsonapi.transformer_mapping');
    }

    /**
     * @return array|string|int
     */
    public function getConfig(string $key = null, mixed $default = null): mixed
    {
        if (!empty($key)) {
            $parts = explode('.', $key);
            $config = $this->config;

            foreach ($parts as $part) {
                $config = $config[$part] ?? [];
            }

            return !empty($config) ? $config : $default;
        }

        return $this->config;
    }
}
