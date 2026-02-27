<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge;

use Psr\SimpleCache\CacheInterface;
use TalesFromADev\TailwindMerge\Helper\Collection;
use TalesFromADev\TailwindMerge\Support\ClassListMerger;
use TalesFromADev\TailwindMerge\Support\Config;

final class TailwindMerge implements TailwindMergeInterface
{
    private ClassListMerger $merger;

    /**
     * @param array<string, mixed> $additionalConfiguration
     */
    public function __construct(
        array $additionalConfiguration = [],
        private readonly ?CacheInterface $cache = null,
    ) {
        Config::setAdditionalConfig($additionalConfiguration);

        $configuration = Config::getMergedConfig();

        $this->merger = new ClassListMerger($configuration);
    }

    /**
     * @param string|list<mixed> ...$classLists
     */
    public function merge(...$classLists): string
    {
        $classList = Collection::make($classLists)->flatten()->join(' ');

        if (!$this->cache instanceof CacheInterface) {
            return $this->merger->merge($classList);
        }

        $key = hash('xxh3', 'tailwind-merge-'.$classList);

        if ($this->cache->has($key)) {
            $cachedValue = $this->cache->get($key);

            if (\is_string($cachedValue)) {
                return $cachedValue;
            }
        }

        $mergedClasses = $this->merger->merge($classList);

        $this->cache->set($key, $mergedClasses);

        return $mergedClasses;
    }
}
