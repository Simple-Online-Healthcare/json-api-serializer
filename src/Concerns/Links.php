<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Concerns;

use SimpleOnlineHealthcare\JsonApi\Contracts\Item;

class Links implements Item
{
    public function __construct(protected ?string $self = null)
    {
    }

    public function getSelf(): ?string
    {
        return $this->self;
    }
}
