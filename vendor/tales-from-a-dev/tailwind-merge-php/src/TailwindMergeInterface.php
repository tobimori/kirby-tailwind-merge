<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge;

interface TailwindMergeInterface
{
    /**
     * @param string|array<array-key, string|array<array-key, string>> ...$args
     */
    public function merge(...$args): string;
}
