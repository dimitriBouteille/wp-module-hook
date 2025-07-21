<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpHook\Attributes;

use Dbout\WpHook\Enums\ActionType;

interface FilterInterface
{
    /**
     * The name of the action/filter to add the callback to.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Used to specify the order in which the functions associated with a particular action/filter are executed.
     * Lower numbers correspond with earlier execution, and functions with the same priority are
     * executed in the order in which they were added to the action.
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * The number of arguments the function accepts.
     *
     * @return int
     */
    public function getAcceptedArgs(): int;

    /**
     * Filter or Action.
     *
     * @return ActionType
     */
    public function getActionType(): ActionType;

    /**
     * List of dependencies on which the action depends.
     *
     * @return array<string|string[]>
     */
    public function getDependencies(): array;
}
