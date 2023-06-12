<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Factories;

use Illuminate\Http\JsonResponse;
use SimpleOnlineHealthcare\JsonApi\Contracts\Entity;
use SimpleOnlineHealthcare\JsonApi\Serializer;

class ResponseFactory
{
    public function __construct(protected Serializer $serializer)
    {
    }

    /**
     * @param Entity|Entity[] $entities
     *
     * @return JsonResponse
     */
    public function make(mixed $entities): JsonResponse
    {
        $data = $this->getSerializer()->toJsonApi($entities);

        return JsonResponse::fromJsonString($data);
    }

    /**
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }
}
