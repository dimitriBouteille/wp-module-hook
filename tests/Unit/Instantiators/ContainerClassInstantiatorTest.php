<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpHook\Tests\Unit\Instantiators;

use Dbout\WpHook\Instantiators\ContainerClassInstantiator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

#[CoversClass(ContainerClassInstantiator::class)]
class ContainerClassInstantiatorTest extends TestCase
{
    public function testInstantiateFromContainer(): void
    {
        $expectedInstance = new \stdClass();
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with(\stdClass::class)
            ->willReturn($expectedInstance);

        $instantiator = new ContainerClassInstantiator($container);
        $result = $instantiator->instantiate(\stdClass::class);

        $this->assertSame($expectedInstance, $result);
    }

    public function testInstantiateWithDependencies(): void
    {
        $dependency = new ServiceDependency();
        $service = new ServiceWithDependency($dependency);

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with(ServiceWithDependency::class)
            ->willReturn($service);

        $instantiator = new ContainerClassInstantiator($container);
        $result = $instantiator->instantiate(ServiceWithDependency::class);

        $this->assertInstanceOf(ServiceWithDependency::class, $result);
        $this->assertSame($dependency, $result->getDependency());
    }

    public function testInstantiateThrowsWhenServiceNotFound(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('get')
            ->willThrowException(new class () extends \Exception implements NotFoundExceptionInterface {});

        $instantiator = new ContainerClassInstantiator($container);

        $this->expectException(NotFoundExceptionInterface::class);
        $instantiator->instantiate('NonExistentService');
    }
}

class ServiceDependency
{
}

class ServiceWithDependency
{
    public function __construct(
        private ServiceDependency $dependency,
    ) {
    }

    public function getDependency(): ServiceDependency
    {
        return $this->dependency;
    }
}
