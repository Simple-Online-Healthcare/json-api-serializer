<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Services;

use Illuminate\Foundation\Application;
use RuntimeException;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;
use SimpleOnlineHealthcare\JsonApi\Contracts\Entity;
use SimpleOnlineHealthcare\JsonApi\Factories\ResponseFactory;
use SimpleOnlineHealthcare\JsonApi\Normalizers\EntityNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;

class SerializerService
{
    protected Serializer $serializer;

    public function __construct(
        protected Application $application,
        protected ResponseFactory $responseFactory,
    ) {
        $encoders = [new JsonEncoder()];

        $normalizers = [
            $this->application->make(EntityNormalizer::class),
            new PropertyNormalizer(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param Entity|Entity[] $entity
     */
    public function toJsonApi(Entity|array $entity, ?Links $links = null): string
    {
        $response = $this->getResponseFactory()->make($entity, $links);

        return $this->getSerializer()->serialize(
            $response,
            JsonEncoder::FORMAT,
            [
                JsonEncode::OPTIONS => JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            ]
        );
    }

    public function fromJsonApi()
    {
        throw new RuntimeException('To be implemented');
    }

    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @return ResponseFactory
     */
    public function getResponseFactory(): ResponseFactory
    {
        return $this->responseFactory;
    }
}
