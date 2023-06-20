<?php

namespace Tests\Concerns\Normalizers;

use Carbon\Carbon;
use DateTimeInterface;
use SimpleOnlineHealthcare\JsonApi\Normalizer;
use Tests\Concerns\Entities\User;

/**
 * @method  getSupportedTypes(?string $format)
 */
class UserNormalizer extends Normalizer
{
    /**
     * @param User $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'type' => 'users',
            'id' => $object->getId(),
            'attributes' => [
                'name' => $object->getName(),
                'email' => $object->getEmail(),
                'createdAt' => $object->getCreatedAt()->format(DateTimeInterface::ATOM),
                'updatedAt' => $object->getUpdatedAt()->format(DateTimeInterface::ATOM),
            ],
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof User;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $attributes = $data['attributes'];

        $data = [
            'id' => $data['id'],
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'createdAt' => Carbon::createFromTimeString($attributes['createdAt']),
            'updatedAt' => Carbon::createFromTimeString($attributes['updatedAt']),
        ];

        return $this->getPropertyNormalizer()->denormalize($data, $type, $format);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return $this->getResourceTypeFromJson($data) === 'users';
    }
}