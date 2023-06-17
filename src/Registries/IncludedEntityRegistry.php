<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Registries;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;

class IncludedEntityRegistry
{
    public function __construct(protected array $entities = [])
    {
    }

    /**
     * @return Entity[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    public function addEntity(Entity $entity): self
    {
        $this->entities[] = $entity;

        return $this;
    }
}
