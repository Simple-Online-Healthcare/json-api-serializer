<?php

namespace SimpleOnlineHealthcare\JsonApi\Contracts;

interface Transformer
{
    public function transform(Entity $entity): array;
}
