<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Registries;

use SimpleOnlineHealthcare\JsonApi\Contracts\Transformer;

use function is_object;

class TransformerRegistry
{
    protected array $map;

    public function __construct(ConfigurationRegistry $configurationRegistry)
    {
        $this->map = $configurationRegistry->getTransformerEntityMap();
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
