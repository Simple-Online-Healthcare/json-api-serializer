<?php

namespace SimpleOnlineHealthcare\JsonApi\Contracts;

use App\Contracts\Entity;

interface Transformer
{
    public function transform(Entity $entity): array;
}
