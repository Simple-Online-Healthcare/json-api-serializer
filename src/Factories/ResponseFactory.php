<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Factories;

use Illuminate\Http\JsonResponse;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Serializer;

class ResponseFactory
{
    public function __construct(
        protected Serializer $serializer,
        protected JsonApiSpecFactory $jsonApiSpecFactory,
    ) {
    }

    /**
     * @param Entity|Entity[] $entities
     */
    public function make(mixed $entities): JsonResponse
    {
        $jsonApiSpec = $this->getJsonApiSpecFactory()->make($entities);

        $data = $this->getSerializer()->toJsonApi($jsonApiSpec);

        return JsonResponse::fromJsonString($data, 200, ['Content-Type' => 'application/vnd.api+json']);
    }

    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }

    public function getJsonApiSpecFactory(): JsonApiSpecFactory
    {
        return $this->jsonApiSpecFactory;
    }
}
