<?php

namespace SimpleOnlineHealthcare\JsonApi\Registries;

use App\Entities\DmdVersion;
use App\JsonApi\Transformers\DmdVersionTransformer;
use SimpleOnlineHealthcare\JsonApi\Contracts\Transformer;

class TransformerRegistry
{
    protected static array $map = [
        DmdVersion::class => DmdVersionTransformer::class,
    ];

    public static function findTransformerByEntity(string $entityClassName): Transformer
    {
        return new self::$map[$entityClassName]();
    }
}
