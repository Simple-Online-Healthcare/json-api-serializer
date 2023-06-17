<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use RuntimeException;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Contracts\Relationship;
use SimpleOnlineHealthcare\JsonApi\Registries\IncludedEntityRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\ResourceTypeRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\TransformerRegistry;
use SimpleOnlineHealthcare\JsonApi\Relationships\HasOne;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

use function array_key_exists;
use function is_string;

/**
 * @method getSupportedTypes(?string $format)
 */
class EntityNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(
        protected TransformerRegistry $transformerRegistry,
        protected ResourceTypeRegistry $resourceTypeRegistry,
        protected PropertyNormalizer $propertyNormalizer,
        protected IncludedEntityRegistry $includedEntityRegistry,
    ) {
    }

    /**
     * @param Entity $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $transformer = $this->getTransformerRegistry()->findTransformerByEntity($object);
        $relationships = $transformer->relationships($object);

        $entity = [
            'type' => $this->getResourceTypeRegistry()->findResourceTypeByEntity($object),
            'id' => $object->getId(),
            'attributes' => $transformer->transform($object),
            'relationships' => $this->restructureRelationships($relationships),
        ];

        return array_filter($entity);
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Entity;
    }

    /**
     * @return Entity|Entity[]
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Entity|array
    {
        $data = $data['data'];

        if (empty($data)) {
            return [];
        }

        $hasOne = is_string(reset($data));

        if ($hasOne === true) {
            $data = [$data];
        }

        $resourceTypeFromJson = $data[0]['type'];
        $entityClass = $this->getResourceTypeRegistry()->findEntityByResourceType($resourceTypeFromJson);

        if ($entityClass !== $type) {
            throw new RuntimeException("Class mismatch: {$entityClass} !== {$type}");
        }

        $transformer = $this->getTransformerRegistry()->findTransformerByEntity($type);

        foreach ($data as $key => $value) {
            $entity = [
                'id' => $value['id'] ?? null,
                ...$value['attributes'],
            ];

            $entity = [
                ...$entity,
                ...$transformer->beforeDenormalize($entity),
            ];

            $data[$key] = $this->getPropertyNormalizer()->denormalize(array_filter($entity), $type, $format, $context);
        }

        if ($hasOne === true) {
            return reset($data);
        }

        return $data;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return array_key_exists('data', $data);
    }

    protected function restructureRelationships(array $relationships): array
    {
        $resourceTypeRegistry = $this->getResourceTypeRegistry();
        $includedEntityRegistry = $this->getIncludedEntityRegistry();

        return array_map(function (Relationship $relationship) use ($resourceTypeRegistry, $includedEntityRegistry) {
            $hasOne = $relationship instanceof HasOne;
            $entities = $relationship->getData();
            $body = [];

            if ($hasOne === true) {
                $entities = [$entities];
            }

            foreach ($entities as $entity) {
                if (empty($entity)) {
                    continue;
                }

                $includedEntityRegistry->addEntity($entity);

                $body[] = [
                    'type' => $resourceTypeRegistry->findResourceTypeByEntity($entity),
                    'id' => $entity->getId(),
                ];
            }

            if ($hasOne === true) {
                return reset($body) ?: [];
            }

            return $body;
        }, $relationships);
    }

    public function getTransformerRegistry(): TransformerRegistry
    {
        return $this->transformerRegistry;
    }

    public function getResourceTypeRegistry(): ResourceTypeRegistry
    {
        return $this->resourceTypeRegistry;
    }

    public function getPropertyNormalizer(): PropertyNormalizer
    {
        return $this->propertyNormalizer;
    }

    public function getIncludedEntityRegistry(): IncludedEntityRegistry
    {
        return $this->includedEntityRegistry;
    }
}
