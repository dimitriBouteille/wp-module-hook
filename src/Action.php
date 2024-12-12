<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpHook;

use Dbout\WpHook\Enums\ActionType;

readonly class Action
{
    public function __construct(
        public string $name,
        public int $priority,
        public int $acceptedArgs,
        public mixed $classInstance,
        public string $methodName,
        public array $dependencies,
        public ActionType $actionType,
    ) {
    }

    /**
     * @return string
     */
    public function getDependencyKey(): string
    {
        $key = get_class($this->classInstance);
        $method = $this->methodName;
        if ($method !== '__invoke') {
            $key .= '::' .$method;
        }

        return $key;
    }
}
