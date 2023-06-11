<?php

namespace SimpleOnlineHealthcare\JsonApi\Registries;

use SimpleOnlineHealthcare\JsonApi\Contracts\Transformer;

class TransformerRegistry
{
    protected array $map;

    public function __construct(ConfigurationRegistry $configurationRegistry)
    {
        $this->map = $configurationRegistry->getTransformerEntityMap();
    }

    /**
     * @param object|string $entity
     *
     * @return Transformer
     */
    public function findTransformerByEntity(mixed $entity): Transformer
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }

        return new $this->map[$entity]();
    }
}
