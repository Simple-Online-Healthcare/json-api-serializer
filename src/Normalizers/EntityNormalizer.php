<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use RuntimeException;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Registries\ResourceTypeRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\TransformerRegistry;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * @method getSupportedTypes(?string $format)
 */
class EntityNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(
        protected TransformerRegistry $transformerRegistry,
        protected ResourceTypeRegistry $resourceTypeRegistry,
        protected PropertyNormalizer $propertyNormalizer,
    ) {
    }

    /**
     * @param Entity $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $transformer = $this->getTransformerRegistry()->findTransformerByEntity($object);

        return [
            'type' => $this->getResourceTypeRegistry()->findResourceTypeByEntity($object),
            'id' => $object->getId(),
            'attributes' => $transformer->transform($object),
        ];
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

    public function getTransformerRegistry(): TransformerRegistry
    {
        return $this->transformerRegistry;
    }

    public function getResourceTypeRegistry(): ResourceTypeRegistry
    {
        return $this->resourceTypeRegistry;
    }

    /**
     * @return PropertyNormalizer
     */
    public function getPropertyNormalizer(): PropertyNormalizer
    {
        return $this->propertyNormalizer;
    }
}
