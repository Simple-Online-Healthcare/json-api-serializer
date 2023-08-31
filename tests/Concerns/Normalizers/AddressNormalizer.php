<?php

namespace Tests\Concerns\Normalizers;

use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;
use SimpleOnlineHealthcare\JsonApi\Fields\Date;
use SimpleOnlineHealthcare\JsonApi\Normalizers\RenderableNormalizer;
use SimpleOnlineHealthcare\JsonApi\Relationships\HasOne;
use Tests\Concerns\Entities\Address;

/**
 * @method  getSupportedTypes(?string $format)
 */
class AddressNormalizer extends RenderableNormalizer
{
    protected string $renderableClassName = Address::class;
    protected string $resourceType = 'addresses';

    public function attributes(Address|Renderable $renderable): array
    {
        return [
            'lineOne' => $renderable->getLineOne(),
            'lineTwo' => $renderable->getLineTwo(),
            'postcode' => $renderable->getPostcode(),
            'createdAt' => new Date($renderable->getCreatedAt()),
            'updatedAt' => new Date($renderable->getUpdatedAt()),
        ];
    }

    public function relationships(Address|Renderable $renderable): array
    {
        return [
            'user' => new HasOne($renderable->getUser()),
        ];
    }
}