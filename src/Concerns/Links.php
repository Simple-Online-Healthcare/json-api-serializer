<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Concerns;

use SimpleOnlineHealthcare\JsonApi\Contracts\Item;

class Links implements Item
{
    protected array $links = [];

    public function __construct(?string $self = null, array $extra = [])
    {
        $this->links = array_merge(['self' => $self], $extra);
    }

    public function getSelf(): ?string
    {
        return $this->links['self'] ?? null;
    }

    public function all(): array
    {
        // Filter out null values (so "prev" can be null)
        return array_filter($this->links, fn($v) => $v !== null);
    }
}

