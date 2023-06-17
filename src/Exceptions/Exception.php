<?php

declare(strict_types=1);

namespace SimpleOnlineHealthcare\JsonApi\Exceptions;

use RuntimeException;
use SimpleOnlineHealthcare\JsonApi\Contracts\RenderableException;

abstract class Exception extends RuntimeException implements RenderableException
{
}
