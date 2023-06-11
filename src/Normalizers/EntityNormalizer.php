<?php

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use SimpleOnlineHealthcare\JsonApi\Contracts\Entity;
use SimpleOnlineHealthcare\JsonApi\Registries\ResourceTypeRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\TransformerRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @method  getSupportedTypes(?string $format)
 */
class EntityNormalizer implements NormalizerInterface
{
    public function __construct(
        protected TransformerRegistry $transformerRegistry,
        protected ResourceTypeRegistry $resourceTypeRegistry,
    ) {
    }

    /**
     * @param Entity      $object
     * @param string|null $format
     * @param array       $context
     * @return array
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
     * @return TransformerRegistry
     */
    public function getTransformerRegistry(): TransformerRegistry
    {
        return $this->transformerRegistry;
    }

    /**
     * @return ResourceTypeRegistry
     */
    public function getResourceTypeRegistry(): ResourceTypeRegistry
    {
        return $this->resourceTypeRegistry;
    }
}
