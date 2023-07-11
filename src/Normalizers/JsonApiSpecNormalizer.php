<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use Closure;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\JsonApiSpec;
use SimpleOnlineHealthcare\JsonApi\Registry;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

use function array_key_exists;
use function is_array;

use const ARRAY_FILTER_USE_BOTH;

/**
 * @method getSupportedTypes(?string $format)
 */
class JsonApiSpecNormalizer implements NormalizerInterface, DenormalizerInterface, NormalizerAwareInterface, SerializerAwareInterface
{
    protected SerializerInterface $serializer;
    protected NormalizerInterface $normalizer;

    /**
     * @var array<class-string, NormalizerInterface>
     */
    protected array $cachedNormalizers;

    public function __construct(
        protected Registry $registry,
        protected ObjectNormalizer $objectNormalizer,
    ) {
    }

    /**
     * @param JsonApiSpec $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        // $value is the JsonApi, Links or Entity|Entities[] objects
        $jsonApi = array_map($this->complexCallback($format), [
            'jsonapi' => $object->getJsonapi(),
            'links' => $object->getLinks(),
            'data' => $object->getData(),
        ]);

        // The relationships have to be run separately due to using an array_map.
        // Essentially, if we'd be using the included entities from the registry before
        // the code started running, it'd always be an empty array.
        $included = [
            'included' => array_map(
                $this->complexCallback($format, ['omitRelations' => true]),
                $this->getRegistry()->getIncludedEntities()
            ),
        ];

        return array_filter($jsonApi + $included, function ($value, $key) {
            // Never filter out the empty data array
            if ($key === 'data') {
                return true;
            }

            return !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function complexCallback(string $format, array $context = []): Closure
    {
        return function (array|object|string|null $value) use ($format, $context) {
            if (empty($value)) {
                return [];
            }

            if (is_array($value) && reset($value) instanceof Entity) {
                $value = array_map($this->complexCallback($format, $context), $value);
            } else {
                $value = $this->normalizeEntity($value, $format, $context);
            }

            if (is_array($value)) {
                return array_filter($value);
            }

            return array_filter(
                $this->getObjectNormalizer()->normalize($value, 'json')
            );
        };
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof JsonApiSpec;
    }

    /**
     * @return Entity|Entity[]
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Entity|array
    {
        $dataFromJsonApi = $data['data'];
        $hasOne = array_key_exists('type', $dataFromJsonApi);

        if ($hasOne) {
            $dataFromJsonApi = [$dataFromJsonApi];
        }

        $denormalisedData = array_map(function (array $data) use ($type) {
            $encodedData = json_encode($data);

            return $this->getSerializer()->deserialize($encodedData, $type, 'json');
        }, $dataFromJsonApi);

        if ($hasOne) {
            return reset($denormalisedData);
        }

        return $denormalisedData;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        $firstValueInData = reset($data);
        $firstValueInInnerData = reset($firstValueInData);

        return array_key_exists('data', $data)
            && (array_key_exists('type', $firstValueInData)
                || array_key_exists('type', $firstValueInInnerData));
    }

    protected function normalizeEntity(object $entity, string $format, array $context = []): object|array
    {
        $entityClassName = $entity::class;

        if (isset($this->cachedNormalizers) && array_key_exists($entityClassName, $this->cachedNormalizers)) {
            return $this->cachedNormalizers[$entityClassName]->normalize($entity, $format);
        }

        /** @var NormalizerInterface $normalizer */
        foreach ($this->getRegistry()->getNormalizers() as $normalizer) {
            if ($normalizer->supportsNormalization($entity, $format)) {
                $this->cachedNormalizers[$entityClassName] = $normalizer;

                return $normalizer->normalize($entity, $format, $context);
            }
        }

        return $entity;
    }

    public function getRegistry(): Registry
    {
        return $this->registry;
    }

    public function getObjectNormalizer(): ObjectNormalizer
    {
        return $this->objectNormalizer;
    }

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function getNormalizer(): NormalizerInterface
    {
        return $this->normalizer;
    }

    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }
}
