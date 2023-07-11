<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Fields;

use DateTime;
use DateTimeInterface;
use SimpleOnlineHealthcare\JsonApi\Contracts\Field;

class Date implements Field
{
    public function __construct(
        protected DateTimeInterface $dateTime,
        protected string $format = DateTimeInterface::ATOM
    ) {
    }

    public function normalize(): string
    {
        return $this->dateTime->format($this->format);
    }

    public function denormalize(): DateTimeInterface
    {
        return new DateTime();
    }
}
