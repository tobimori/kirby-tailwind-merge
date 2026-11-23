<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
trait ValidateArbitraryVariable
{
    /**
     * @param string|array<array-key, string> $labels
     */
    protected static function getIsArbitraryVariable(string $value, string|array $labels, bool $shouldMatchNoLabel = false): bool
    {
        if ('(' !== ($value[0] ?? '')) {
            return false;
        }

        if (1 !== preg_match(self::ARBITRARY_VARIABLE_REGEX, $value, $matches, \PREG_UNMATCHED_AS_NULL)) {
            return false;
        }

        // See ValidatesArbitraryValue.
        if (null !== $matches[1]) {
            return \in_array($matches[1], \is_string($labels) ? [$labels] : $labels);
        }

        return $shouldMatchNoLabel;
    }
}
