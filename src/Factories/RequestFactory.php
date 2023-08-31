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
    public function make(mixed $entities): string
    {
        return parent::toJsonApi($entities, [RenderableNormalizer::OMIT_ID => true]);
    }
}
