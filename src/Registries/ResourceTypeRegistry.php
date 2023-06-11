<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Registries;

use function is_object;

class ResourceTypeRegistry
{
    protected array $map;

    public function __construct(ConfigurationRegistry $configurationRegistry)
    {
        $this->map = $configurationRegistry->getTransformerEntityMap();
    }

    /**
     * @param object|string $entity
     */
    public function findResourceTypeByEntity(mixed $entity): string
    {
        if (is_object($entity)) {
            $entity = $entity::class;
        }

        return $this->map[$entity];
    }
}
