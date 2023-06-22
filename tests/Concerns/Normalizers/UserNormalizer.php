<?php

namespace Tests\Concerns\Normalizers;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Fields\Date;
use SimpleOnlineHealthcare\JsonApi\Normalizers\EntityNormalizer;
use SimpleOnlineHealthcare\JsonApi\Relationships\HasOne;
use Tests\Concerns\Entities\User;

/**
 * @method  getSupportedTypes(?string $format)
 */
class UserNormalizer extends EntityNormalizer
{
    protected string $entityClassName = User::class;
    protected string $resourceType = 'users';

    public function attributes(User|Entity $entity): array
    {
        return [
            'name' => $entity->getName(),
            'email' => $entity->getEmail(),
            'createdAt' => new Date($entity->getCreatedAt()),
            'updatedAt' => new Date($entity->getUpdatedAt()),
        ];
    }

    public function relationships(User|Entity $entity): array
    {
        return [
            'address' => new HasOne($entity->getAddress(), 'addresses'),
        ];
    }
}