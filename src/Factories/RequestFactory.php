<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Factories;

use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;
use SimpleOnlineHealthcare\JsonApi\Normalizers\RenderableNormalizer;

class RequestFactory extends SchemaFactory
{
    /**
     * @param Renderable|Renderable[] $entities
     */
    public function make(mixed $entities, bool $omitId = true): string
    {
        $jsonApiString = parent::toJsonApi($entities, [RenderableNormalizer::OMIT_ID => $omitId]);
        $jsonApi = json_decode($jsonApiString, true);

        $data = $jsonApi['data'] ?? [];

        return json_encode(['data' => $data], JSON_PRESERVE_ZERO_FRACTION);
    }
}
