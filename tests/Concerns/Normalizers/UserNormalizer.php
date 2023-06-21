<?php

namespace Tests\Concerns\Normalizers;

use Carbon\Carbon;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Fields\Date;
use SimpleOnlineHealthcare\JsonApi\Normalizers\EntityNormalizer;
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
            'address' => $entity->getAddress(),
        ];
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $attributes = $data['attributes'];

        $data = [
            'id' => $data['id'],
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'createdAt' => Carbon::createFromTimeString($attributes['createdAt']),
            'updatedAt' => Carbon::createFromTimeString($attributes['updatedAt']),
        ];

        return $this->getPropertyNormalizer()->denormalize($data, $type, $format);
    }
}