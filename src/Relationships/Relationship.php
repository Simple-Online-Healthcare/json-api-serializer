<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Relationships;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Contracts\Relationship as RelationshipContract;

abstract class Relationship implements RelationshipContract
{
    /**
     * @param Entity|Entity[]|null $data
     */
    public function __construct(protected Entity|array|null $data)
    {
    }

    /**
     * @return Entity|Entity[]|null
     */
    public function getData(): Entity|array|null
    {
        return $this->data;
    }
}
