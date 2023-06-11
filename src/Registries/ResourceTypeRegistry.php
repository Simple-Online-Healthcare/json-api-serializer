<?php

namespace SimpleOnlineHealthcare\JsonApi\Registries;

class ResourceTypeRegistry
{
    protected array $map;

    public function __construct(ConfigurationRegistry $configurationRegistry)
    {
        $this->map = $configurationRegistry->getTransformerEntityMap();
    }

    /**
     * @param object|string $entity
     *
     * @return string
     */
    public function findResourceTypeByEntity(mixed $entity): string
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }

        return $this->map[$entity];
    }
}
