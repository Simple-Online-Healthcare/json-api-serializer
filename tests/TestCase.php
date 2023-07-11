<?php

namespace Tests;

use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase as BaseTestCase;
use SimpleOnlineHealthcare\JsonApi\SerializerServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected Application $application;

    protected function setUp(): void
    {
        $this->application = new Application();

        (new SerializerServiceProvider($this->application))->register();
    }

    protected function setProtectedAttribute(object $object, string $attribute, mixed $value): void
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($attribute);
        $reflectionProperty->setValue($object, $value);
    }
}