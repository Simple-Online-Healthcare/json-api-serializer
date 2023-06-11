<?php

namespace SimpleOnlineHealthcare\JsonApi;

use App\Contracts\Entity;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;

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

    public static function make(mixed $data): Response
    {
        return new self(new JsonApi(), new Links(), $data);
    }
}
