<?php

namespace SimpleOnlineHealthcare\JsonApi\Concerns;

use SimpleOnlineHealthcare\JsonApi\Contracts\Item;

class JsonApi implements Item
{
    protected string $version = '1.0';

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return JsonApi
     */
    public function setVersion(string $version): JsonApi
    {
        $this->version = $version;

        return $this;
    }
}
