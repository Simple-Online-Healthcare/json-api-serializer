<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Contracts\Field;
use SimpleOnlineHealthcare\JsonApi\Contracts\Relationship;
use SimpleOnlineHealthcare\JsonApi\Relationships\HasOne;

abstract class EntityNormalizer extends Normalizer
{
    /**
     * @var class-string $entityClassName
     */
    protected string $entityClassName;

    /**
     * @var string $resourceType
     */
    protected string $resourceType;

    /**
     * Returns the ID of the Entity
     */
    public function id(Entity $entity): string|int
    {
        return $entity->getId();
    }

    /**
     * Returns the attributes to be shown in the JSON:API response
     */
    abstract public function attributes(Entity $entity): array;

    /**
     * Returns the relationships of the entity
     *
     * @return Relationship[]
     */
    public function relationships(Entity $entity): array
    {
        return [];
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $attributes = $this->attributes($object);
        $relationships = $this->normalizeRelationships($this->relationships($object));

        return array_filter([
            'type' => $this->resourceType,
            'id' => $this->id($object),
            'attributes' => $this->normalizeFields($attributes),
            'relationships' => $context['omitRelations'] !== true ? array_filter($relationships) : [],
        ]);
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof $this->entityClassName;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return $this->getResourceTypeFromJson($data) === $this->resourceType;
    }

    protected function normalizeFields(array $fields): array
    {
        return array_map(function (mixed $field) {
            if (!$field instanceof Field) {
                return $field;
            }

            return $field->normalize();
        }, $fields);
    }

    protected function normalizeRelationships(array $relationships): array
    {
        $buffer = [];
        $relationBuffer = [];

        /**  @var Relationship $relationship */
        foreach ($relationships as $key => $relationship) {
            $hasOne = $relationship instanceof HasOne;
            $relation = $relationship->getData();

            if (empty($relation)) {
                return [];
            }

            if ($hasOne) {
                $relation = [$relation];
            }

            foreach ($relation as $relationItem) {
                $relationBuffer[] = $this->structureRelationship(
                    $relationship->getResourceType() ?? $key,
                    $relationItem->getId()
                );

                $this->registry->addToIncludedEntities($relationItem);
            }

            if ($hasOne) {
                $relationBuffer = reset($relationBuffer);
            }

            $buffer[$key] = $relationBuffer;
        }

        return $buffer;
    }

    protected function structureRelationship(string $resourceType, string|int $id): array
    {
        return [
            'type' => $resourceType,
            'id' => $id,
        ];
    }
}
