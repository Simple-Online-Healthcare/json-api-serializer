<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use Illuminate\Foundation\Application;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;
use SimpleOnlineHealthcare\JsonApi\Factories\JsonApiSpecFactory;
use SimpleOnlineHealthcare\JsonApi\Normalizers\EntityNormalizer;
use SimpleOnlineHealthcare\JsonApi\Normalizers\JsonApiSpecNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer as BaseSerializer;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;

class Serializer
{
    protected BaseSerializer $serializer;

    public function __construct(
        protected Application $application,
        protected JsonApiSpecFactory $jsonApiSpecFactory,
    ) {
        $encoders = [new JsonEncoder()];

        $normalizers = [
            $this->application->make(EntityNormalizer::class),
            new PropertyNormalizer(),
        ];

        $this->serializer = new BaseSerializer($normalizers, $encoders);
    }

    /**
     * @param Entity|Entity[] $entity
     */
    public function toJsonApi(Entity|array $entity, ?Links $links = null): string
    {
        $response = $this->getJsonApiSpecFactory()->make($entity, $links);

        return $this->getSerializer()->serialize(
            $response,
            JsonEncoder::FORMAT,
            [
                JsonEncode::OPTIONS => JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            ]
        );
    }

    public function fromJsonApi(string $json, string $class)
    {
        return $this->getSerializer()->deserialize($json, $class, 'json');
    }

    protected function getSerializer(): BaseSerializer
    {
        return $this->serializer;
    }

    protected function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @return JsonApiSpecFactory
     */
    protected function getJsonApiSpecFactory(): JsonApiSpecFactory
    {
        return $this->jsonApiSpecFactory;
    }
}
