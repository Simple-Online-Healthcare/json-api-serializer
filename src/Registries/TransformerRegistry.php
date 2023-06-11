<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Registries;

use SimpleOnlineHealthcare\JsonApi\Contracts\Transformer;

use function is_object;

class TransformerRegistry
{
    public function __construct(protected array $map)
    {
    }

    /**
     * @param object|string $entity
     */
    public function findTransformerByEntity(mixed $entity): Transformer
    {
        if (is_object($entity)) {
            $entity = $entity::class;
        }

        return new $this->map[$entity]();
    }
}
