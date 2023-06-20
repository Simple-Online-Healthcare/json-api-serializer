<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Concerns\Included;
use SimpleOnlineHealthcare\JsonApi\Registry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * @method getSupportedTypes(?string $format)
 */
class IncludedNormalizer implements NormalizerInterface
{
    public function __construct(
        protected Registry $registry,
        protected PropertyNormalizer $propertyNormalizer,
    ) {
    }

    /**
     * @param Entity $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $entities = $this->getRegistry()->getIncludedEntities();

        if (empty($entities)) {
            return [];
        }

        $included = [];

        foreach ($entities as $entity) {
            $transformer = $this->getRegistry()->findTransformerByEntity($entity);

            $included[] = [
                'type' => $this->getRegistry()->findResourceTypeByEntity($entity),
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

    /**
     * @return Registry
     */
    public function getRegistry(): Registry
    {
        return $this->registry;
    }

    public function getPropertyNormalizer(): PropertyNormalizer
    {
        return $this->propertyNormalizer;
    }
}
