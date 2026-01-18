<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpHook\Tests\Unit\Instantiators;

use Dbout\WpHook\Instantiators\ReflectionClassInstantiator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReflectionClassInstantiator::class)]
class ReflectionClassInstantiatorTest extends TestCase
{
    public function testInstantiateWithoutConstructor(): void
    {
        $instantiator = new ReflectionClassInstantiator();
        $instance = $instantiator->instantiate(ClassWithoutConstructor::class);

        $this->assertInstanceOf(ClassWithoutConstructor::class, $instance);
    }

    public function testInstantiateBypassesConstructor(): void
    {
        $instantiator = new ReflectionClassInstantiator();
        $instance = $instantiator->instantiate(ClassWithConstructor::class);

        $this->assertInstanceOf(ClassWithConstructor::class, $instance);
        $this->assertFalse($instance->wasConstructorCalled());
    }

    public function testInstantiateThrowsExceptionForNonExistentClass(): void
    {
        $instantiator = new ReflectionClassInstantiator();

        $this->expectException(\ReflectionException::class);
        $instantiator->instantiate('NonExistentClass');
    }
}

class ClassWithoutConstructor
{
}

class ClassWithConstructor
{
    private bool $constructorCalled = false;

    public function __construct()
    {
        $this->constructorCalled = true;
    }

    public function wasConstructorCalled(): bool
    {
        return $this->constructorCalled;
    }
}
