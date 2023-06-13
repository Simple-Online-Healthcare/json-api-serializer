<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Registries\ResourceTypeRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\TransformerRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @method getSupportedTypes(?string $format)
 */
class EntityNormalizer implements NormalizerInterface
{
    public function __construct(
        protected TransformerRegistry $transformerRegistry,
        protected ResourceTypeRegistry $resourceTypeRegistry,
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

    public function getTransformerRegistry(): TransformerRegistry
    {
        return $this->transformerRegistry;
    }

    public function getResourceTypeRegistry(): ResourceTypeRegistry
    {
        return $this->resourceTypeRegistry;
    }
}
