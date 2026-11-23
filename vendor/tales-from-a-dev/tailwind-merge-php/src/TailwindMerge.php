<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge;

use Psr\SimpleCache\CacheInterface;
use TalesFromADev\TailwindMerge\Support\ClassListMerger;
use TalesFromADev\TailwindMerge\Support\Config;
use TalesFromADev\TailwindMerge\Support\LruCache;

final class TailwindMerge implements TailwindMergeInterface
{
    /**
     * Shared by every instance built without additional configuration, so the
     * class map is built once per process.
     */
    private static ?ClassListMerger $defaultMerger = null;

    private ClassListMerger $merger;

    private ?LruCache $lruCache = null;

    /**
     * Embeds a configuration fingerprint so instances sharing a cache pool
     * cannot read each other's results.
     */
    private string $cacheKeyPrefix;

    /**
     * @param array<string, mixed> $additionalConfiguration
     */
    public function __construct(
        array $additionalConfiguration = [],
        private readonly ?CacheInterface $cache = null,
    ) {
        Config::setAdditionalConfig($additionalConfiguration);

        $configuration = Config::getMergedConfig();

        // The class map depends on nothing else, so other options keep the precompiled one.
        $useDefaultClassMap = !\array_key_exists('theme', $additionalConfiguration) && !\array_key_exists('classGroups', $additionalConfiguration);

        $this->merger = [] === $additionalConfiguration
            ? self::$defaultMerger ??= new ClassListMerger($configuration, true)
            : new ClassListMerger($configuration, $useDefaultClassMap);
        $this->cacheKeyPrefix = 'tailwind-merge-'.self::fingerprint($additionalConfiguration).'-';

        // Fronts an injected PSR-16 pool: a round trip per merge is too costly (see #15).
        if ($configuration['cacheSize'] > 0) {
            $this->lruCache = new LruCache($configuration['cacheSize']);
        }
    }

    /**
     * @param string|list<mixed> ...$classLists
     */
    public function merge(...$classLists): string
    {
        $classList = implode(' ', self::flatten($classLists));

        if (!$this->cache instanceof CacheInterface && !$this->lruCache instanceof LruCache) {
            return $this->merger->merge($classList);
        }

        $key = hash('xxh3', $this->cacheKeyPrefix.$classList);

        $cachedValue = $this->getCached($key);

        if (null !== $cachedValue) {
            return $cachedValue;
        }

        $mergedClasses = $this->merger->merge($classList);

        $this->setCached($key, $mergedClasses);

        return $mergedClasses;
    }

    private function getCached(string $key): ?string
    {
        $cachedValue = $this->lruCache?->get($key);

        if (null !== $cachedValue) {
            return $cachedValue;
        }

        // Not has() then get(): that doubles the round trips.
        $cachedValue = $this->cache?->get($key);

        if (!\is_string($cachedValue)) {
            return null;
        }

        $this->lruCache?->set($key, $cachedValue);

        return $cachedValue;
    }

    private function setCached(string $key, string $value): void
    {
        $this->lruCache?->set($key, $value);
        $this->cache?->set($key, $value);
    }

    /**
     * @param array<array-key, mixed> $classLists
     *
     * @return list<string>
     */
    private static function flatten(array $classLists): array
    {
        $flattened = [];

        foreach ($classLists as $classList) {
            if (\is_array($classList)) {
                array_push($flattened, ...self::flatten($classList));
            } elseif (\is_scalar($classList) || null === $classList || $classList instanceof \Stringable) {
                $flattened[] = (string) $classList;
            } else {
                throw new \TypeError(\sprintf('Class lists must be strings or arrays of strings, %s given.', get_debug_type($classList)));
            }
        }

        return $flattened;
    }

    /**
     * Must be stable across processes for a persistent cache pool, which rules
     * out spl_object_id and friends.
     *
     * @param array<array-key, mixed> $configuration
     */
    private static function fingerprint(array $configuration): string
    {
        if ([] === $configuration) {
            return 'default';
        }

        return hash('xxh3', self::describe($configuration));
    }

    private static function describe(mixed $value): string
    {
        if (\is_array($value)) {
            $parts = [];
            foreach ($value as $key => $item) {
                $parts[] = $key.':'.self::describe($item);
            }

            return '['.implode(',', $parts).']';
        }

        if ($value instanceof \Closure) {
            // Not serializable, but its definition site is stable for a given release.
            $reflection = new \ReflectionFunction($value);

            return 'fn@'.$reflection->getFileName().':'.$reflection->getStartLine();
        }

        if (\is_object($value)) {
            return $value::class.'('.implode(',', array_map(
                static fn (mixed $property): string => self::describe($property),
                get_object_vars($value),
            )).')';
        }

        if (\is_scalar($value) || null === $value) {
            return var_export($value, true);
        }

        return \gettype($value);
    }
}
