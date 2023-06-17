<?php

namespace Tests\Concerns\Transformers;

use Carbon\Carbon;
use DateTimeInterface;
use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Contracts\Transformer;
use SimpleOnlineHealthcare\JsonApi\Relationships\HasOne;
use Tests\Concerns\Entities\Address;

class AddressTransformer implements Transformer
{
    public function transform(Address|Entity $entity): array
    {
        return [
            'lineOne' => $entity->getLineOne(),
            'lineTwo' => $entity->getLineTwo(),
            'postcode' => $entity->getPostcode(),
            'createdAt' => $entity->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updatedAt' => $entity->getUpdatedAt()->format(DateTimeInterface::ATOM),
        ];
    }

    public function relationships(Address|Entity $entity): array
    {
        return [
            'user' => new HasOne($entity->getUser()),
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