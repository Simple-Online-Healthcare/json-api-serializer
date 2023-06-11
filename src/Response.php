<?php

namespace SimpleOnlineHealthcare\JsonApi;

use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;
use SimpleOnlineHealthcare\JsonApi\Contracts\Entity;

class Response
{
    /**
     * @param JsonApi         $jsonApi
     * @param Links           $links
     * @param Entity|Entity[] $data
     */
    public function __construct(
        protected JsonApi $jsonApi,
        protected Links $links,
        protected Entity|array $data,
    ) {
    }
}
