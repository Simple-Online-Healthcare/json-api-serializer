<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use Closure;
use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;
use SimpleOnlineHealthcare\JsonApi\JsonApiSpec;
use SimpleOnlineHealthcare\JsonApi\Registry;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

use function array_key_exists;
use function in_array;
use function is_array;

use const ARRAY_FILTER_USE_BOTH;

/**
 * @method getSupportedTypes(?string $format)
 */
class JsonApiSpecNormalizer implements NormalizerInterface, DenormalizerInterface, NormalizerAwareInterface,
                                       SerializerAwareInterface
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
        // $value is the JsonApi, Links or Renderable|Entities[] objects
        $jsonApi = array_map($this->complexCallback($format, $context), [
            'jsonapi' => $object->getJsonapi(),
            'links' => $object->getLinks() ?? null,
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
            if (in_array($key, ['data', 'links'])) {
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

            if (is_array($value) && reset($value) instanceof Renderable) {
                $value = array_map($this->complexCallback($format, $context), $value);
            } else {
                $value = $this->normalizeRenderable($value, $format, $context);
            }

            if (is_array($value)) {
                return array_filter($value);
            }

            return array_filter(
                $this->getObjectNormalizer()->normalize($value, 'json', $context)
            );
        };
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof JsonApiSpec;
    }

    /**
     * @return Renderable|Renderable[]
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Renderable|array
    {
        $dataFromJsonApi = $data['data'];
        $hasOne = array_key_exists('type', $dataFromJsonApi);

        if ($hasOne) {
            $dataFromJsonApi = [$dataFromJsonApi];
        }

        $denormalizedData = array_map(function (array $data) use ($type, $context) {
            $encodedData = json_encode($data);

            return $this->getSerializer()->deserialize($encodedData, $type, 'json', $context);
        }, $dataFromJsonApi);

        if ($hasOne) {
            return reset($denormalizedData);
        }

        return $denormalizedData;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        $firstValueInData = reset($data);
        $firstValueInInnerData = reset($firstValueInData);

        return array_key_exists('data', $data)
            && (array_key_exists('type', $firstValueInData)
                || array_key_exists('type', $firstValueInInnerData));
    }

    protected function normalizeRenderable(object $renderable, string $format, array $context = []): object|array
    {
        $renderableClassName = $renderable::class;

        if (isset($this->cachedNormalizers) && array_key_exists($renderableClassName, $this->cachedNormalizers)) {
            return $this->cachedNormalizers[$renderableClassName]->normalize($renderable, $format);
        }

        /** @var NormalizerInterface $normalizer */
        foreach ($this->getRegistry()->getNormalizers() as $normalizer) {
            if ($normalizer->supportsNormalization($renderable, $format)) {
                $this->cachedNormalizers[$renderableClassName] = $normalizer;

                return $normalizer->normalize($renderable, $format, $context);
            }
        }

        return $renderable;
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
