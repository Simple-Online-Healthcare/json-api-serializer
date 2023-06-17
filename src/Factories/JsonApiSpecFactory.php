<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Factories;

use SimpleOnlineHealthcare\JsonApi\Concerns\Included;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;
use SimpleOnlineHealthcare\JsonApi\JsonApiSpec;

class JsonApiSpecFactory
{
    public function __construct(
        protected JsonApi $jsonApi,
        protected Included $included,
    ) {
    }

    public function make(mixed $data, Links $links = null): JsonApiSpec
    {
        return new JsonApiSpec($this->getJsonApi(), $links, $data, $this->getIncluded());
    }

    public function getJsonApi(): JsonApi
    {
        return $this->jsonApi;
    }

    /**
     * @return Included
     */
    public function getIncluded(): Included
    {
        return $this->included;
    }
}
