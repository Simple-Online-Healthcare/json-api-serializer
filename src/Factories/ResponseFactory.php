<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Factories;

use Illuminate\Http\JsonResponse;
use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;

class ResponseFactory extends SchemaFactory
{
    /**
     * @param Renderable|Renderable[] $entities
     */
    public function make(mixed $entities, int $statusCode = 200): JsonResponse
    {
        $data = parent::toJsonApi($entities);

        return JsonResponse::fromJsonString($data, $statusCode, ['Content-Type' => 'application/vnd.api+json']);
    }
}
