<?php

namespace Tests\Concerns\Transformers;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Contracts\Transformer;
use Tests\Concerns\Entities\User;

class UserTransformer implements Transformer
{
    public function transform(User|Entity $entity): array
    {
        return [
            'name' => $entity->getName(),
            'email' => $entity->getEmail(),
            'password' => $entity->getPassword(),
        ];
    }
}