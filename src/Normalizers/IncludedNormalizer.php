<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Concerns\Included;
use SimpleOnlineHealthcare\JsonApi\Registries\IncludedEntityRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\ResourceTypeRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\TransformerRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * @method getSupportedTypes(?string $format)
 */
class IncludedNormalizer implements NormalizerInterface
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
        $entities = $this->getIncludedEntityRegistry()->getEntities();

        if (empty($entities)) {
            return [];
        }

        $included = [];

        foreach ($entities as $entity) {
            $transformer = $this->getTransformerRegistry()->findTransformerByEntity($entity);

            $included[] = [
                'type' => $this->getResourceTypeRegistry()->findResourceTypeByEntity($entity),
                'id' => $entity->getId(),
                'attributes' => $transformer->transform($entity),
            ];
        }

        return $included;
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Included;
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
