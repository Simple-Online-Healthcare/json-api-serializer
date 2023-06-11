<?php

namespace SimpleOnlineHealthcare\JsonApi\Services;

use Illuminate\Foundation\Application;
use RuntimeException;
use SimpleOnlineHealthcare\JsonApi\Contracts\Entity;
use SimpleOnlineHealthcare\JsonApi\Normalizers\EntityNormalizer;
use SimpleOnlineHealthcare\JsonApi\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerService
{
    protected Serializer $serializer;

    public function __construct(protected Application $application)
    {
        $encoders = [new JsonEncoder()];

        $normalizers = [
            $this->application->make(EntityNormalizer::class),
            new PropertyNormalizer(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param Entity|Entity[] $entity
     *
     * @return string
     */
    public function toJsonApi(Entity|array $entity): string
    {
        $response = Response::make($entity);

        return $this->getSerializer()->serialize(
            $response,
            JsonEncoder::FORMAT,
            [JsonEncode::OPTIONS => JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT]
        );
    }

    public function fromJsonApi()
    {
        throw new RuntimeException('To be implemented');
    }

    /**
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }
}
