<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpHook\Instantiators;

/**
 * Interface for class instantiation strategies.
 *
 * Allows different strategies for instantiating hook classes,
 * such as using reflection (default) or a dependency injection container.
 */
interface ClassInstantiatorInterface
{
    /**
     * Instantiates a class by its fully qualified class name.
     *
     * @template T of object
     * @param class-string<T> $className The fully qualified class name to instantiate
     * @return T The instantiated object
     */
    public function instantiate(string $className): object;
}
