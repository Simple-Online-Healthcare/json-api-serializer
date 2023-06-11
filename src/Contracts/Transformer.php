<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Contracts;

interface Transformer
{
    public function transform(Entity $entity): array;
}
