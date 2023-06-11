<?php

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use App\Contracts\Entity;
use App\JsonApi\Transformers\DmdVersionTransformer;
use SimpleOnlineHealthcare\JsonApi\Enums\JsonApiEntityEnum;
use SimpleOnlineHealthcare\JsonApi\Registries\TransformerRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @method  getSupportedTypes(?string $format)
 */
class EntityNormalizer implements NormalizerInterface
{
    /**
     * @param Entity      $object
     * @param string|null $format
     * @param array       $context
     * @return array
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $className = get_class($object);
        
        $transformer = TransformerRegistry::findTransformerByEntity($className);

        return [
            'type' => JsonApiEntityEnum::convertClassToResourceType($className),
            'id' => $object->getId(),
            'attributes' => $transformer->transform($object)
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Entity;
    }
}
