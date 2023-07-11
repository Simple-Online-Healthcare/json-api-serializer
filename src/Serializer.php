<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use SimpleOnlineHealthcare\JsonApi\Factories\JsonApiSpecFactory;
use SimpleOnlineHealthcare\JsonApi\Normalizers\JsonApiSpecNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
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
            ];

        $this->serializer = new BaseSerializer($normalizers, $encoders);
    }

    public function toJsonApi(JsonApiSpec $jsonApiSpec): string
    {
        return $this->getSerializer()->serialize(
            $jsonApiSpec,
            JsonEncoder::FORMAT,
            [
                JsonEncode::OPTIONS => JSON_UNESCAPED_UNICODE,
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            ]
        );
    }

    public function fromJsonApi(string $json, string $class)
    {
        return $this->getSerializer()->deserialize($json, $class, JsonEncoder::FORMAT);
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
