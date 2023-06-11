<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Concerns;

use SimpleOnlineHealthcare\JsonApi\Contracts\Item;

class JsonApi implements Item
{
    public function __construct(protected string $version)
    {
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
