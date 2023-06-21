<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Normalizers;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

use function array_key_exists;

abstract class Normalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(protected PropertyNormalizer $propertyNormalizer)
    {
    }

    protected function getResourceTypeFromJson(array $data): ?string
    {
        // if the data key exists, it means the main request is going to fall in here
        // which isn't correct. only the entity JSON should ever fall in here.
        if (array_key_exists('data', $data)) {
            return null;
        }

        if (array_key_exists('type', $data) === false) {
            $data = reset($data);
        }

        return $data['type'] ?? null;
    }

    protected function getPropertyNormalizer(): PropertyNormalizer
    {
        return $this->propertyNormalizer;
    }
}
