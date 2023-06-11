<?php

namespace SimpleOnlineHealthcare\JsonApi\Concerns;

use SimpleOnlineHealthcare\JsonApi\Contracts\Item;

class Links implements Item
{
    public function __construct(protected ?string $self = null)
    {
    }

    /**
     * @return string|null
     */
    public function getSelf(): ?string
    {
        return $this->self;
    }
}
