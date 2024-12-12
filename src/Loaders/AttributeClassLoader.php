<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpHook\Loaders;

use Dbout\WpHook\Action;
use Dbout\WpHook\Attributes\Action as ActionAttribute;
use Dbout\WpHook\Attributes\Filter as FilterAttribute;
use Dbout\WpHook\Attributes\FilterInterface;
use Dbout\WpHook\Exceptions\HookException;

class AttributeClassLoader implements InterfaceLoader
{
    /**
     * @inheritDoc
     */
    public function load(mixed $resource): array
    {
        if (!class_exists($resource)) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" does not exist.', $resource));
        }

        $class = new \ReflectionClass($resource);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(\sprintf(
                'Attributes from class "%s" cannot be read as it is abstract.',
                $class->getName()
            ));
        }

        $classFilter = $this->getClassFilter($class);
        if ($classFilter instanceof FilterInterface) {
            return [$this->buildAction($classFilter, $class, '__invoke')];
        }

        $filters = [];
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodFilter = $this->getMethodFilter($method);
            if ($methodFilter instanceof FilterInterface) {
                $filters[] = $this->buildAction($methodFilter, $class, $method->getName());
            }
        }

        return $filters;
    }

    /**
     * @param \ReflectionClass $class
     * @throws HookException
     * @return FilterInterface|null
     */
    protected function getClassFilter(\ReflectionClass $class): ?FilterInterface
    {
        [$filter, $action] = $this->getAttribute($class);
        if ($action === null && $filter === null) {
            return null;
        }

        if ($filter instanceof FilterAttribute && $action instanceof ActionAttribute) {
            throw new HookException(sprintf(
                'You cannot use the Action and Filter attributes on the same class %s.',
                $class->getName()
            ));
        }

        if (!$class->hasMethod('__invoke')) {
            throw new HookException(sprintf('Class "%s" does not have a method "__invoke".', $class->getName()));
        }

        return $filter ?? $action;
    }

    /**
     * @param \ReflectionMethod $method
     * @throws HookException
     * @return FilterInterface|null
     */
    protected function getMethodFilter(\ReflectionMethod $method): ?FilterInterface
    {
        [$filter, $action] = $this->getAttribute($method);
        if ($action === null && $filter === null) {
            return null;
        }

        if ($filter instanceof FilterAttribute && $action instanceof ActionAttribute) {
            throw new HookException(sprintf(
                'You cannot use the Action and Filter attributes on the same method %s::%s.',
                $method->getDeclaringClass(),
                $method->getName()
            ));
        }

        return $filter ?? $action;
    }

    /**
     * @param \ReflectionMethod|\ReflectionClass $subject
     * @return array<FilterInterface|null>
     */
    protected function getAttribute(\ReflectionMethod|\ReflectionClass $subject): array
    {
        $filter =  $subject->getAttributes(FilterAttribute::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
        $action = $subject->getAttributes(ActionAttribute::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;

        return [
            $filter?->newInstance(),
            $action?->newInstance(),
        ];
    }

    /**
     * @param FilterInterface $filter
     * @param \ReflectionClass $class
     * @param string $fncName
     * @throws HookException
     * @throws \ReflectionException
     * @return Action
     */
    protected function buildAction(
        FilterInterface $filter,
        \ReflectionClass $class,
        string $fncName
    ): Action {

        $dependencies = [];
        foreach ($filter->getDependencies() as $dependency) {
            if (is_array($dependency)) {
                $dependencies[] = implode('::', $dependency);
            } elseif (is_string($dependency)) {
                $dependencies[] = $dependency;
            } else {
                throw new HookException(sprintf('Hook %s: invalid dependency format.', $class->getName()));
            }
        }

        return new Action(
            name: $filter->getName(),
            priority: $filter->getPriority(),
            acceptedArgs: $filter->getAcceptedArgs(),
            classInstance: $class->newInstanceWithoutConstructor(),
            methodName: $fncName,
            dependencies: $dependencies,
            actionType: $filter->getActionType(),
        );
    }
}
