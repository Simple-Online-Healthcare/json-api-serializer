<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;
use SimpleOnlineHealthcare\JsonApi\Exceptions\InvalidJsonApiDocument;
use SimpleOnlineHealthcare\JsonApi\Factories\JsonApiSpecFactory;
use SimpleOnlineHealthcare\JsonApi\Normalizers\JsonApiSpecNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as BaseSerializer;

use const JSON_UNESCAPED_UNICODE;

class Serializer
{
    protected BaseSerializer $serializer;

    public function __construct(
        protected Registry $registry,
        protected JsonApiSpecFactory $jsonApiSpecFactory,
    ) {
        $encoders = [new JsonEncoder()];

        $normalizers = $this->getRegistry()->getNormalizers() + [
                new JsonApiSpecNormalizer($this->getRegistry(), new ObjectNormalizer()),
                new DateTimeNormalizer(),
            ];

        $this->serializer = new BaseSerializer($normalizers, $encoders);
    }

    public function toJsonApi(JsonApiSpec $jsonApiSpec, array $context = []): string
    {
        return $this->getSerializer()->serialize(
            $jsonApiSpec,
            JsonEncoder::FORMAT,
            [
                JsonEncode::OPTIONS => JSON_UNESCAPED_UNICODE,
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,

                // Allow the above values to be overwritten.
                ...$context,
            ]
        );
    }

    public function fromJsonApi(string $json, ?string $class = null, ?Renderable $objectToPopulate = null)
    {
        if (empty($class)) {
            $class = $this->guessRenderableClassName($json);
        }

        return $this->getSerializer()->deserialize($json, $class, JsonEncoder::FORMAT, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $objectToPopulate,
            // AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,
        ]);
    }

    protected function guessRenderableClassName(string $json): string
    {
        $decoded = json_decode($json, true);

        if (empty($decoded['data'])) {
            throw new InvalidJsonApiDocument();
        }

        $data = $decoded['data'];
        $resourceType = $data['type'] ?? $data[0]['type'];

        return $this->registry->getEntityByResourceType($resourceType);
    }

    public function getSerializer(): BaseSerializer
    {
        return $this->serializer;
    }

    public function getRegistry(): Registry
    {
        return $this->registry;
    }

    protected function getJsonApiSpecFactory(): JsonApiSpecFactory
    {
        return $this->jsonApiSpecFactory;
    }
}
