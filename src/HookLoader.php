<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpHook;

use Dbout\WpHook\Enums\ActionType;
use Dbout\WpHook\Exceptions\HookException;
use Dbout\WpHook\FileLocators\FileLocator;
use Dbout\WpHook\Helpers\SortActions;
use Dbout\WpHook\Loaders\AttributeDirectoryLoader;
use Psr\Cache\CacheItemPoolInterface;

class HookLoader
{
    private const CACHE_KEY = '_app_wp_autoloader_hooks';

    /**
     * @param string|array<string> $directory
     * @param CacheItemPoolInterface|null $cache
     */
    public function __construct(
        protected string|array $directory,
        public ?CacheItemPoolInterface $cache = null,
    ) {
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @return Action[]
     */
    protected function getHooks(): array
    {
        if (!$this->cache instanceof CacheItemPoolInterface) {
            return $this->findHooks();
        }

        $cacheRoutes = $this->cache->getItem(self::CACHE_KEY);
        if ($cacheRoutes->isHit()) {
            try {
                return unserialize($cacheRoutes->get());
            } catch (\Exception) {
            }
        }

        $routes = $this->findHooks();
        $cacheRoutes->set(serialize($routes));
        $this->cache->save($cacheRoutes);
        return $routes;
    }

    /**
     * @throws \Exception
     * @return Action[]
     */
    protected function findHooks(): array
    {
        $directories = $this->getDirectories();

        $hooks = [];
        foreach ($directories as $directory) {
            $loader = new AttributeDirectoryLoader(
                new FileLocator([$directory])
            );

            $hooks = array_merge($hooks, $loader->load($directory));
        }

        return (new SortActions())->sort($hooks);
    }

    /**
     * @throws HookException
     * @return array<string>
     */
    protected function getDirectories(): array
    {
        $tmpDirectories = is_array($this->directory) ? $this->directory : [$this->directory];

        $directories = [];
        foreach ($tmpDirectories as $dir) {
            $globalDirs = glob($dir);
            if (!is_array($globalDirs)) {
                continue;
            }

            $directories = array_merge($directories, $globalDirs);
        }

        $dirs = [];
        foreach ($directories as $directory) {
            $directory = new \SplFileInfo($directory);
            if (!$directory->isDir()) {
                throw new HookException(sprintf(
                    'The path %s is not a valid folder.',
                    $directory
                ));
            }

            $path = $directory->getRealPath();
            if (!is_string($path)) {
                continue;
            }

            $dirs[] = $path;
        }

        return $dirs;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @return void
     */
    public function register(): void
    {
        $hooks = $this->getHooks();

        foreach ($hooks as $hook) {
            match ($hook->actionType) {
                ActionType::Action => add_action(...$this->getHookArg($hook)),
                ActionType::Filter => add_filter(...$this->getHookArg($hook)),
            };
        }
    }

    /**
     * @param Action $action
     * @return array<string, mixed>
     */
    protected function getHookArg(Action $action): array
    {
        return [
            'hook_name' => $action->name,
            'callback' => [$action->classInstance, $action->methodName],
            'priority' => $action->priority,
            'accepted_args' => $action->acceptedArgs,
        ];
    }
}
