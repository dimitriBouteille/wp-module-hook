<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpHook\Attributes;

use Dbout\WpHook\Enums\ActionType;

/**
 * - [WP add_action](https://developer.wordpress.org/reference/functions/add_action/)
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class Action implements FilterInterface
{
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
