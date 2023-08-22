<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use SimpleOnlineHealthcare\JsonApi\Concerns\Included;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;
use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;

class JsonApiSpec
{
    /**
     * @param Renderable|Renderable[] $data
     */
    public function __construct(
        protected JsonApi $jsonapi,
        protected ?Links $links,
        protected Renderable|array $data,
        protected Included $included,
    ) {
    }

    public function getJsonapi(): JsonApi
    {
        return $this->jsonapi;
    }

    public function getLinks(): ?Links
    {
        return $this->links;
    }

    public function getData(): Renderable|array
    {
        return $this->data;
    }

    public function getIncluded(): Included
    {
        return $this->included;
    }
}
