<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Contracts;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;

interface Transformer
{
    public function transform(Entity $entity): array;
}
