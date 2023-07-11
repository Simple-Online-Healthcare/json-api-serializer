<?php

namespace Tests\Concerns\Normalizers;

use Carbon\Carbon;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Fields\Date;
use SimpleOnlineHealthcare\JsonApi\Normalizers\EntityNormalizer;
use SimpleOnlineHealthcare\JsonApi\Relationships\HasOne;
use Tests\Concerns\Entities\Address;

/**
 * @method  getSupportedTypes(?string $format)
 */
class AddressNormalizer extends EntityNormalizer
{
    protected string $entityClassName = Address::class;
    protected string $resourceType = 'addresses';

    public function attributes(Address|Entity $entity): array
    {
        return [
            'lineOne' => $entity->getLineOne(),
            'lineTwo' => $entity->getLineTwo(),
            'postcode' => $entity->getPostcode(),
            'createdAt' => new Date($entity->getCreatedAt()),
            'updatedAt' => new Date($entity->getUpdatedAt()),
        ];
    }

    public function relationships(Address|Entity $entity): array
    {
        return [
            'user' => new HasOne($entity->getUser()),
        ];
    }
}