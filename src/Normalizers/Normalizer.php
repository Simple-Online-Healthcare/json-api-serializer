<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use SimpleOnlineHealthcare\JsonApi\Registry;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

abstract class Normalizer implements NormalizerInterface, DenormalizerInterface
{
    protected Registry $registry;

    public function __construct(protected PropertyNormalizer $propertyNormalizer)
    {
    }

    protected function getPropertyNormalizer(): PropertyNormalizer
    {
        return $this->propertyNormalizer;
    }

    public function setRegistry(Registry $registry): self
    {
        $this->registry = $registry;

        return $this;
    }
}
