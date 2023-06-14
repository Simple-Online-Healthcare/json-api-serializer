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
     * @return class-string
     */
    public function findResourceTypeByEntity(mixed $entity): string
    {
        if (is_object($entity)) {
            $entity = $entity::class;
        }

        return $this->map[$entity];
    }

    /**
     * @return class-string
     */
    public function findEntityByResourceType(string $resourceType): string
    {
        return array_flip($this->map)[$resourceType];
    }
}
