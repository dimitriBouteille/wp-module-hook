<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpHook\Helpers;

use Dbout\WpHook\Action;

class SortActions
{
    /**
     * @param Action[] $items
     * @throws \Exception
     * @return Action[]
     */
    public function sort(array $items): array
    {
        $result = [];
        $doneList = [];

        while (count($items) > count($result)) {
            $doneSomething = false;

            foreach ($items as $item) {
                if (isset($doneList[$item->getDependencyKey()])) {
                    // item already in result set
                    continue;
                }

                $resolved = true;
                $dependencies = $item->dependencies;

                if ($dependencies != []) {
                    foreach ($dependencies as $dep) {
                        if (!isset($doneList[$dep])) {
                            // there is a dependency that is not met:
                            $resolved = false;
                            break;
                        }
                    }
                }

                if ($resolved) {
                    //all dependencies are met:
                    $doneList[$item->getDependencyKey()] = true;
                    $result[] = $item;
                    $doneSomething = true;
                }
            }

            if (!$doneSomething) {
                throw new \Exception('Unresolvable dependency');
            }
        }

        return $result;
    }
}
