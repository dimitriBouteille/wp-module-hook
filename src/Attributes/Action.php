<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpHook\Attributes;

use Dbout\WpHook\Enums\ActionType;

/**
 * @see https://developer.wordpress.org/reference/functions/add_action/
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class Action implements FilterInterface
{
    /**
     * @param string $name The name of the action to add the callback to.
     * @param int $priority Used to specify the order in which the functions associated with a particular action are executed.
     * @param int $acceptedArgs The number of arguments the function accepts.
     * @param array<string|string[]> $dependencies List of dependencies on which the action depends.
     */
    public function __construct(
        protected string $name,
        protected int $priority = 10,
        protected int $acceptedArgs = 1,
        protected array $dependencies = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function getAcceptedArgs(): int
    {
        return $this->acceptedArgs;
    }

    /**
     * @inheritDoc
     */
    public function getActionType(): ActionType
    {
        return ActionType::Action;
    }

    /**
     * @inheritDoc
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}
