<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

/**
 * @internal
 */
final class LruCache
{
    private int $cacheSize = 0;

    /**
     * @var array<string, string>
     */
    private array $cache = [];

    /**
     * @var array<string, string>
     */
    private array $previousCache = [];

    public function __construct(public readonly int $maxCacheSize)
    {
    }

    public function get(string $key): ?string
    {
        // Not a truthiness test: '' and '0' are valid merge results.
        $value = $this->cache[$key] ?? null;

        if (null !== $value) {
            return $value;
        }

        $value = $this->previousCache[$key] ?? null;

        if (null !== $value) {
            $this->update($key, $value);

            return $value;
        }

        return null;
    }

    public function set(string $key, string $value): void
    {
        if (\array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $value;

            return;
        }

        $this->update($key, $value);
    }

    private function update(string $key, string $value): void
    {
        $this->cache[$key] = $value;
        ++$this->cacheSize;

        if ($this->cacheSize > $this->maxCacheSize) {
            $this->cacheSize = 0;
            $this->previousCache = $this->cache;
            $this->cache = [];
        }
    }
}
