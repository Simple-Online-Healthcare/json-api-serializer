<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use Illuminate\Foundation\Application;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Exceptions\NoResourceTypeFoundForEntity;

use function array_key_exists;
use function in_array;
use function is_object;

class Registry
{
    public function __construct(
        protected Application $application,
        protected array $resourceTypeMapping,
        protected array $normalizerMapping,
        protected array $includedEntities = [],
    ) {
        // Instantiate the normalisers
        $this->normalizerMapping = array_map(function (string $className) {
            return $this->application->make($className)->setRegistry($this);
        }, $this->normalizerMapping);
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

    public function getResourceTypeMapping(): array
    {
        return $this->resourceTypeMapping;
    }

    public function getEntityByResourceType(string $resourceType): string
    {
        return array_flip($this->getResourceTypeMapping())[$resourceType];
    }

    public function getNormalizers(): array
    {
        return $this->normalizerMapping;
    }

    public function getIncludedEntities(): array
    {
        return $this->includedEntities;
    }

    public function addToIncludedEntities(Entity $entity): void
    {
        if (in_array($entity, $this->includedEntities, true)) {
            return;
        }

        $this->includedEntities[] = $entity;
    }
}
