<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Encoders;

use SimpleOnlineHealthcare\JsonApi\Factories\JsonApiSpecFactory;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class JsonApiEncoder extends JsonEncoder
{
    public const FORMAT = 'json:api';

    protected JsonApiSpecFactory $jsonApiSpecFactory;

    public function __construct(
        JsonApiSpecFactory $jsonApiSpecFactory,
        JsonEncode $encodingImpl = null,
        JsonDecode $decodingImpl = null,
        array $defaultContext = []
    ) {
        $this->jsonApiSpecFactory = $jsonApiSpecFactory;

        parent::__construct($encodingImpl, $decodingImpl, $defaultContext);
    }

    public function encode(mixed $data, string $format, array $context = []): string
    {
        $jsonApiSpec = $this->jsonApiSpecFactory->make($data);

        return parent::encode($jsonApiSpec, 'json');
    }

    public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }

    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
