<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use Error;
use RuntimeException;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Contracts\Field;
use SimpleOnlineHealthcare\JsonApi\Contracts\Relationship;
use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;
use SimpleOnlineHealthcare\JsonApi\Exceptions\InvalidRenderableIdImplementation;
use SimpleOnlineHealthcare\JsonApi\Relationships\EmptyRelation;
use SimpleOnlineHealthcare\JsonApi\Relationships\HasOne;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

use function array_key_exists;

abstract class RenderableNormalizer extends Normalizer implements SerializerAwareInterface
{
    use SerializerAwareTrait;

    public const OMIT_ID = 'omit_id';

    /**
     * @var class-string $renderableClassName
     */
    protected string $renderableClassName;

    protected string $resourceType;

    /**
     * Returns the ID of the Entity.
     */
    public function id(Renderable $renderable, bool $omitId = false): string|int
    {
        if ($renderable instanceof Entity) {
            try {
                return $renderable->getId();
            } catch (Error $error) {
                if ($omitId === true) {
                    return 0;
                }

                throw $error;
            }
        }

        throw new InvalidRenderableIdImplementation();
    }

    /**
     * Returns the attributes to be shown in the JSON:API response.
     */
    abstract public function attributes(Renderable $renderable): array;

    /**
     * Returns the relationships of the entity.
     *
     * @return Relationship[]
     */
    public function relationships(Renderable $renderable): array
    {
        return [];
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $attributes = $this->attributes($object);
        $relationships = $this->normalizeRelationships($this->relationships($object));
        $shouldOmitRelations = $context['omitRelations'] ?? false;

        return array_filter([
            'type' => $this->resourceType,
            'id' => $this->id($object, $context[self::OMIT_ID] ?? false),
            'attributes' => $this->normalizeFields($attributes),
            'relationships' => $shouldOmitRelations === false ? $relationships : [],
        ]);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $id = $data['id'];
        $attributes = $data['attributes'];

        $creatingNewEntity = ($id ?? null) === null;

        $renderableArray = $attributes;

        if ($creatingNewEntity === false) {
            $objectToPopulate = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? [];

            if (!empty($objectToPopulate)) {
                if ($objectToPopulate instanceof Entity) {
                    if ($objectToPopulate->getId() !== (int)$id) {
                        throw new RuntimeException('id mismatch');
                    }
                }

                $propertyNormalizer = $this->getPropertyNormalizer();

                $propertyNormalizer->setSerializer($this->serializer);
                $objectToPopulate = $propertyNormalizer->normalize($objectToPopulate);
            }

            $renderableArray = [
                ...$objectToPopulate,
                ...$attributes,

                'id' => $id ?? null,
            ];
        }

        return $this->getPropertyNormalizer()->denormalize(array_filter($renderableArray), $type, $format);
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof $this->renderableClassName;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return $this->getResourceTypeFromJson($data) === $this->resourceType;
    }

    protected function getResourceTypeFromJson(array $data): ?string
    {
        // if the data key exists, it means the main request is going to fall in here
        // which isn't correct. only the entity JSON should ever fall in here.
        if (array_key_exists('data', $data)) {
            return null;
        }

        if (array_key_exists('type', $data) === false) {
            $data = reset($data);
        }

        return $data['type'] ?? null;
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

        /** @var Relationship $relationship */
        foreach ($relationships as $key => $relationship) {
            $relationBuffer = [];

            $hasOne = $relationship instanceof HasOne;
            $relation = $relationship->getData();

            if (empty($relation)) {
                // This line essentially just ensures that the relationship key is always
                // present, regardless is the relationship is empty or not.
                $value = [];

                if ($relationship instanceof HasOne) {
                    $value = new EmptyRelation();
                }

                $buffer[$key] = $value;

                continue;
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
