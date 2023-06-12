<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Factories;

use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;
use SimpleOnlineHealthcare\JsonApi\JsonApiSpec;

class JsonApiSpecFactory
{
    public function __construct(protected JsonApi $jsonApi)
    {
    }

    public function make(mixed $data, Links $links = null): JsonApiSpec
    {
        return new JsonApiSpec($this->getJsonApi(), $links, $data);
    }

    public function getJsonApi(): JsonApi
    {
        return $this->jsonApi;
    }
}
