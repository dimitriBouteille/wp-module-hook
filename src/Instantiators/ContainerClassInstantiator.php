<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpHook\Instantiators;

use Psr\Container\ContainerInterface;

/**
 * Class instantiator using a PSR-11 container.
 *
 * Allows hook classes to be instantiated through a dependency injection container,
 * enabling constructor injection and proper dependency management.
 */
class ContainerClassInstantiator implements ClassInstantiatorInterface
{
    /**
     * @param ContainerInterface $container The PSR-11 container to use for instantiation
     */
    public function __construct(
        protected ContainerInterface $container,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function instantiate(string $className): object
    {
        return $this->container->get($className);
    }
}
