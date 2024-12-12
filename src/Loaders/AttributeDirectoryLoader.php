<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpHook\Loaders;

use Dbout\WpHook\FileLocators\FileLocatorInterface;

class AttributeDirectoryLoader
{
    /**
     * @param FileLocatorInterface $fileLocator
     * @param AttributeClassLoader $loader
     */
    public function __construct(
        protected FileLocatorInterface $fileLocator,
        protected AttributeClassLoader $loader = new AttributeClassLoader(),
    ) {
        if (!\function_exists('token_get_all')) {
            throw new \LogicException('The Tokenizer extension is required for the routing attribute loader.');
        }
    }

    /**
     * @param string $file
     * @throws \Exception
     * @return array
     */
    public function load(string $file): array
    {
        $dir = $this->fileLocator->locate($file);
        if (is_array($dir) || !is_dir($dir)) {
            return [];
        }

        /** @var \SplFileInfo[] $files */
        $files = iterator_to_array(new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS),
                fn (\SplFileInfo $current): bool => !str_starts_with($current->getBasename(), '.'),
            ),
            \RecursiveIteratorIterator::LEAVES_ONLY
        ));

        usort($files, function (\SplFileInfo $a, \SplFileInfo $b): int {
            return (string) $a > (string) $b ? 1 : -1;
        });

        $hooks = [];
        foreach ($files as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            /** @var class-string|null $class */
            $class = $this->findClass($file);
            if ($class === null || $class === '' || !class_exists($class)) {
                continue;
            }

            $ref = new \ReflectionClass($class);
            if ($ref->isAbstract()) {
                continue;
            }

            $hooks = array_merge(
                $hooks,
                $this->loader->load($class)
            );
        }

        return $hooks;
    }

    /**
     * @param \SplFileInfo $file
     * @return string|null
     */
    protected function findClass(\SplFileInfo $file): ?string
    {
        $class = false;
        $namespace = false;
        $tokens = token_get_all(file_get_contents($file->getRealPath()));

        if (1 === \count($tokens) && \T_INLINE_HTML === $tokens[0][0]) {
            throw new \InvalidArgumentException(\sprintf('The file "%s" does not contain PHP code. Did you forgot to add the "<?php" start tag at the beginning of the file?', $file));
        }

        $nsTokens = [\T_NS_SEPARATOR => true, \T_STRING => true];
        if (\defined('T_NAME_QUALIFIED')) {
            $nsTokens[\T_NAME_QUALIFIED] = true;
        }

        for ($i = 0; isset($tokens[$i]); ++$i) {
            $token = $tokens[$i];
            if (!isset($token[1])) {
                continue;
            }

            if ($class && \T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
            }

            if (true === $namespace && isset($nsTokens[$token[0]])) {
                $namespace = $token[1];
                while (isset($tokens[++$i][1], $nsTokens[$tokens[$i][0]])) {
                    $namespace .= $tokens[$i][1];
                }
                $token = $tokens[$i];
            }

            if (\T_CLASS === $token[0]) {
                // Skip usage of ::class constant and anonymous classes
                $skipClassToken = false;

                for ($j = $i - 1; $j > 0; --$j) {
                    if (!isset($tokens[$j][1])) {
                        if ('(' === $tokens[$j] || ',' === $tokens[$j]) {
                            $skipClassToken = true;
                        }
                        break;
                    }

                    if (\T_DOUBLE_COLON === $tokens[$j][0] || \T_NEW === $tokens[$j][0]) {
                        $skipClassToken = true;
                        break;
                    } elseif (!\in_array($tokens[$j][0], [\T_WHITESPACE, \T_DOC_COMMENT, \T_COMMENT], true)) {
                        break;
                    }
                }

                if (!$skipClassToken) {
                    $class = true;
                }
            }

            if (\T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        return null;
    }
}
