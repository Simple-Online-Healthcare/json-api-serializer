<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use Illuminate\Foundation\Application;
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
        protected Application $application,
        protected Registry $registry,
        protected JsonApiSpecFactory $jsonApiSpecFactory,
    ) {
        $encoders = [new JsonEncoder()];

        $normalizers = $this->instantiateApplicationNormalizers() + [
                new JsonApiSpecNormalizer(new ObjectNormalizer()),
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

    protected function instantiateApplicationNormalizers(): array
    {
        return array_map(function (string $className) {
            return $this->getApplication()->make($className);
        }, $this->getRegistry()->getNormalizers());
    }

    public function getSerializer(): BaseSerializer
    {
        return $this->serializer;
    }

    public function getApplication(): Application
    {
        return $this->application;
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
