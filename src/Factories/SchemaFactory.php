<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Factories;

use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;
use SimpleOnlineHealthcare\JsonApi\Serializer;

abstract class SchemaFactory
{
    public function __construct(
        protected Serializer $serializer,
        protected JsonApiSpecFactory $jsonApiSpecFactory,
    ) {
    }

    /**
     * @param Renderable|Renderable[] $entities
     */
    public function toJsonApi(mixed $entities, array $context = []): string
    {
        $jsonApiSpec = $this->getJsonApiSpecFactory()->make($entities);

        return $this->getSerializer()->toJsonApi($jsonApiSpec, $context);
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
