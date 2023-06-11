<?php

namespace SimpleOnlineHealthcare\JsonApi\Concerns;

use SimpleOnlineHealthcare\JsonApi\Contracts\Item;

class JsonApi implements Item
{
    public function __construct(protected string $version)
    {
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
