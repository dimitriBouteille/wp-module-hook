<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpHook\Loaders;

interface InterfaceLoader
{
    /**
     * @param mixed $resource
     * @throws \Exception
     * @return mixed
     */
    public function load(mixed $resource): mixed;
}
