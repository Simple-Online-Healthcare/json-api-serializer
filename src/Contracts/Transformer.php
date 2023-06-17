<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Contracts;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;

interface Transformer
{
    /**
     * @return string[]
     */
    public function transform(Entity $entity): array;

    /**
     * @return Relationship[]
     */
    public function relationships(Entity $entity): array;

    /**
     * @return object[]
     */
    public function beforeDenormalize(array $entity): array;
}
