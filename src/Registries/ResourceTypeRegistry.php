<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Registries;

use function is_object;

class ResourceTypeRegistry
{
    public function __construct(protected array $map)
    {
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
