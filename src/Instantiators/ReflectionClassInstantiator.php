<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpHook\Instantiators;

/**
 * Default class instantiator using reflection.
 *
 * Instantiates classes without calling their constructor,
 * which is the original behavior of the package.
 */
class ReflectionClassInstantiator implements ClassInstantiatorInterface
{
    /**
     * @inheritDoc
     */
    public function instantiate(string $className): object
    {
        $reflectionClass = new \ReflectionClass($className);

        return $reflectionClass->newInstanceWithoutConstructor();
    }
}
