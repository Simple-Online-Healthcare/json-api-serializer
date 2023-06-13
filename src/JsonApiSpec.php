<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi;

use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Concerns\Links;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;

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
}
