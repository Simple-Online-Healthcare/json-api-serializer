<?php

namespace Tests\Concerns\Normalizers;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;
use SimpleOnlineHealthcare\JsonApi\Fields\Date;
use SimpleOnlineHealthcare\JsonApi\Normalizers\RenderableNormalizer;
use SimpleOnlineHealthcare\JsonApi\Relationships\HasOne;
use Tests\Concerns\Entities\User;

/**
 * @method  getSupportedTypes(?string $format)
 */
class UserNormalizer extends RenderableNormalizer
{
    protected string $renderableClassName = User::class;
    protected string $resourceType = 'users';

    public function attributes(User|Renderable $renderable): array
    {
        return [
            'name' => $renderable->getName(),
            'email' => $renderable->getEmail(),
            'createdAt' => new Date($renderable->getCreatedAt()),
            'updatedAt' => new Date($renderable->getUpdatedAt()),
        ];
    }

    public function relationships(User|Renderable $renderable): array
    {
        return [
            'address' => new HasOne($renderable->getAddress(), 'addresses'),
        ];
    }
}