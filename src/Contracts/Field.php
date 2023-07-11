<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Contracts;

interface Field
{
    public function normalize();

    public function denormalize();
}
