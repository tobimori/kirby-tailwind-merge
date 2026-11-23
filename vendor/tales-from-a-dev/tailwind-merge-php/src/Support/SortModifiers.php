<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

/**
 * @internal
 */
final class SortModifiers
{
    /**
     * @var array<string, int>
     */
    private array $modifierWeights = [];

    /**
     * @param list<string> $sensitiveModifiers
     */
    public function __construct(array $sensitiveModifiers)
    {
        foreach ($sensitiveModifiers as $index => $modifier) {
            $this->modifierWeights[$modifier] = 1000000 + $index;
        }
    }

    /**
     * @param array<array-key, string> $modifiers
     *
     * @return array<array-key, string>
     */
    public function sort(array $modifiers): array
    {
        $sortedModifiers = [];
        $unsortedModifiers = [];

        foreach ($modifiers as $modifier) {
            $isPositionSensitive = '[' === $modifier[0] || isset($this->modifierWeights[$modifier]);

            if ($isPositionSensitive) {
                sort($unsortedModifiers);
                array_push($sortedModifiers, ...$unsortedModifiers);

                $sortedModifiers[] = $modifier;
                $unsortedModifiers = [];
            } else {
                $unsortedModifiers[] = $modifier;
            }
        }

        sort($unsortedModifiers);

        return [...$sortedModifiers, ...$unsortedModifiers];
    }
}
