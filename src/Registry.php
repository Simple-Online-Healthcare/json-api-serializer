<?php

namespace SimpleOnlineHealthcare\JsonApi;

use SimpleOnlineHealthcare\JsonApi\Contracts\Transformer;
use SimpleOnlineHealthcare\JsonApi\Exceptions\NoResourceTypeFoundForEntity;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;

class Registry
{
    public function __construct(
        protected array $resourceTypeMapping,
        protected array $transformerMapping,
        protected array $includedEntityMapping = [],
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

    /**
     * @param object|string $entity
     */
    public function findTransformerByEntity(mixed $entity): Transformer
    {
        if (is_object($entity)) {
            $entity = $entity::class;
        }

        return new $this->transformerMapping[$entity]();
    }

    /**
     * @return Entity[]
     */
    public function getIncludedEntities(): array
    {
        return $this->includedEntityMapping;
    }

    public function addIncludedEntity(Entity $entity): self
    {
        $this->includedEntityMapping[] = $entity;

        return $this;
    }
}