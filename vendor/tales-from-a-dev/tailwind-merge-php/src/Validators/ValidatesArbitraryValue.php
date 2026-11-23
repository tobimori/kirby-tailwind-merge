<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
trait ValidatesArbitraryValue
{
    /**
     * @param string|array<array-key, string> $labels
     * @param callable(string): bool          $isLengthOnly
     */
    protected static function getIsArbitraryValue(string $value, string|array $labels, callable $isLengthOnly): bool
    {
        if ('[' !== ($value[0] ?? '')) {
            return false;
        }

        // PREG_UNMATCHED_AS_NULL keeps an absent label distinguishable.
        if (1 !== preg_match(self::ARBITRARY_VALUE_REGEX, $value, $matches, \PREG_UNMATCHED_AS_NULL)) {
            return false;
        }

        // Null check, not truthiness: in the upstream JS a "0" label is truthy.
        if (null !== $matches[1]) {
            return \in_array($matches[1], \is_string($labels) ? [$labels] : $labels);
        }

        return $isLengthOnly($matches[2]);
    }
}
