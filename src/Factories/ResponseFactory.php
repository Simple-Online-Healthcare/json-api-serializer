<?php

namespace SimpleOnlineHealthcare\JsonApi\Factories;

use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;
use SimpleOnlineHealthcare\JsonApi\Response;

class ResponseFactory
{
    public function __construct(protected JsonApi $jsonApi)
    {
    }

    public function make(mixed $data, ?Links $links = null): Response
    {
        return new Response($this->getJsonApi(), $links, $data);
    }

    /**
     * @return JsonApi
     */
    public function getJsonApi(): JsonApi
    {
        return $this->jsonApi;
    }
}