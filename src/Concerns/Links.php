<?php

namespace SimpleOnlineHealthcare\JsonApi\Concerns;

use SimpleOnlineHealthcare\JsonApi\Contracts\Item;

class Links implements Item
{
    protected string $self;

    /**
     * @return string
     */
    public function getSelf(): string
    {
        return $this->self;
    }

    /**
     * @param string $self
     * @return Links
     */
    public function setSelf(string $self): Links
    {
        $this->self = $self;

        return $this;
    }
}
