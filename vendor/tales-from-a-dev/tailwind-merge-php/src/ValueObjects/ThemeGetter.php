<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\ValueObjects;

final class ThemeGetter
{
    public function __construct(
        public string $key,
    ) {
    }

    /**
     * @param array<string, list<mixed>> $theme
     *
     * @return list<mixed>
     */
    public function get(array $theme): array
    {
        return $theme[$this->key] ?? [];
    }
}
