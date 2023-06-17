<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Contracts;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;

interface Relationship
{
    /**
     * @return Entity|Entity[]|null
     */
    public function getData(): Entity|array|null;
}
