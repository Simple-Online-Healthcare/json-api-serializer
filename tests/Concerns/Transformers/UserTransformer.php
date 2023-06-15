<?php

namespace Tests\Concerns\Transformers;

use Carbon\Carbon;
use DateTimeInterface;
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
            'createdAt' => $entity->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updatedAt' => $entity->getUpdatedAt()->format(DateTimeInterface::ATOM),
        ];
    }

    public function beforeDenormalize(array $entity): array
    {
        return [
            'createdAt' => Carbon::createFromTimeString($entity['createdAt']),
            'updatedAt' => Carbon::createFromTimeString($entity['updatedAt']),
        ];
    }
}