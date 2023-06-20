<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use SimpleOnlineHealthcare\JsonApi\Exceptions\NoResourceTypeFoundForEntity;

use function array_key_exists;
use function is_object;

class Registry
{
    public function __construct(
        protected array $resourceTypeMapping,
        protected array $normalizerMapping,
    ) {
    }

    /**
     * @return class-string
     */
    public function findResourceTypeByEntity(mixed $entity): string
    {
        if (is_object($entity)) {
            $entity = $entity::class;
        }

        if (array_key_exists($entity, $this->resourceTypeMapping) === false) {
            throw new NoResourceTypeFoundForEntity();
        }

        return $this->resourceTypeMapping[$entity];
    }

    /**
     * @return class-string
     */
    public function findEntityByResourceType(string $resourceType): string
    {
        return array_flip($this->resourceTypeMapping)[$resourceType];
    }

    public function getNormalizers(): array
    {
        return $this->normalizerMapping;
    }
}
