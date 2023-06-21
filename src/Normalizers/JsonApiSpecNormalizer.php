<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use RuntimeException;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\JsonApiSpec;
use SimpleOnlineHealthcare\JsonApi\Registry;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

use function array_key_exists;
use function is_array;
use function is_string;

/**
 * @method getSupportedTypes(?string $format)
 */
class JsonApiSpecNormalizer implements NormalizerInterface, DenormalizerInterface, NormalizerAwareInterface,
                                       SerializerAwareInterface
{
    protected SerializerInterface $serializer;
    protected NormalizerInterface $normalizer;

    public function __construct(
        protected Registry $registry,
        protected ObjectNormalizer $objectNormalizer,
    ) {
    }

    /**
     * @param JsonApiSpec $object
     * @throws ExceptionInterface
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $data = $object->getData();
        $hasOne = $data instanceof Entity;

        if ($hasOne) {
            $data = [$data];
        }

        $data = array_map(function (Entity $entity) use ($format) {
            /** @var NormalizerInterface $normalizer */
            foreach ($this->getRegistry()->getNormalizers() as $normalizer) {
                if ($normalizer->supportsNormalization($entity, $format)) {
                    return $normalizer->normalize($entity, $format);
                }
            }

            $className = get_class($entity);

            throw new RuntimeException("No normaliser found for {$className}");
        }, $data);

        // $value is the JsonApi, Links or Entity|Entities[] objects
        $jsonApi = array_map(function (array|object|string|null $value) {
            if (empty($value)) {
                return [];
            }

            if (is_string($value)) {
                if (json_decode($value, true) === false) {
                    throw new RuntimeException('Not valid json!');
                }

                return $value;
            }

            if (!is_array($value)) {
                $value = $this->getObjectNormalizer()->normalize($value, 'json');
            }

            return array_filter($value);
        }, [
            'jsonapi' => $object->getJsonapi(),
            'links' => $object->getLinks(),
            'data' => $hasOne ? reset($data) : $data,
            'included' => $this->getRegistry()->getIncludedEntities(),
        ]);

        return array_filter($jsonApi);
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

    /**
     * @return Registry
     */
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
