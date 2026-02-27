<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use function Symfony\Component\String\u;

/**
 * @internal
 */
trait ValidatesArbitraryValue
{
    /**
     * @param string|array<array-key, string> $labels
     */
    protected static function getIsArbitraryValue(string $value, string|array $labels, callable $isLengthOnly): bool
    {
        $labels = \is_string($labels) ? [$labels] : $labels;

        $matches = u($value)->match(self::ARBITRARY_VALUE_REGEX);

        if ([] !== $matches) {
            if ('' !== $matches[1] && '0' !== $matches[1] && null !== $matches[1]) {
                return \in_array($matches[1], $labels);
            }

            return $isLengthOnly($matches[2] ?? null);
        }

        return false;
    }
}
