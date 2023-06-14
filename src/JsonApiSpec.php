<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
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
    ) {
    }

    /**
     * @return JsonApi
     */
    public function getJsonapi(): JsonApi
    {
        return $this->jsonapi;
    }

    /**
     * @return Links|null
     */
    public function getLinks(): ?Links
    {
        return $this->links;
    }

    /**
     * @return array|Entity
     */
    public function getData(): Entity|array
    {
        return $this->data;
    }
}
