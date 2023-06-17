<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Concerns\Included;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;

class JsonApiSpec
{
    /**
     * @param Entity|Entity[] $data
     */
    public function __construct(
        protected JsonApi $jsonapi,
        protected ?Links $links,
        protected Entity|array $data,
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

    public function getData(): Entity|array
    {
        return $this->data;
    }
}
