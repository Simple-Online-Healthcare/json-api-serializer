<?php

namespace Tests\Concerns\Normalizers;

use SimpleOnlineHealthcare\JsonApi\Normalizers\Normalizer;

/**
 * @method  getSupportedTypes(?string $format)
 */
class AddressNormalizer extends Normalizer
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        // TODO: Implement denormalize() method.
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        // TODO: Implement supportsDenormalization() method.
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        // TODO: Implement normalize() method.
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        // TODO: Implement supportsNormalization() method.
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method  getSupportedTypes(?string $format)
    }
}