<?php

namespace Tests;

use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase as BaseTestCase;
use SimpleOnlineHealthcare\JsonApi\Registries\ResourceTypeRegistry;
use SimpleOnlineHealthcare\JsonApi\Registries\TransformerRegistry;
use Tests\Concerns\Entities\User;
use Tests\Concerns\Transformers\UserTransformer;

abstract class TestCase extends BaseTestCase
{
    protected Application $application;

    protected function setUp(): void
    {
        $this->application = new Application();

        // todo: find a better way to do this
        $this->runMiniServiceProvider();
    }

    protected function runMiniServiceProvider()
    {
        $this->application->singleton(TransformerRegistry::class, function () {
            return new TransformerRegistry([User::class => UserTransformer::class]);
        });

        $this->application->singleton(ResourceTypeRegistry::class, function () {
            return new ResourceTypeRegistry([User::class => 'users']);
        });
    }

    protected function setProtectedAttribute(object $object, string $attribute, mixed $value): void
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($attribute);
        $reflectionProperty->setValue($object, $value);
    }
}