<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use function Symfony\Component\String\u;

trait ValidateArbitraryVariable
{
    /**
     * @param string|array<array-key, string> $labels
     */
    protected static function getIsArbitraryVariable(string $value, string|array $labels, bool $shouldMatchNoLabel = false): bool
    {
        $labels = \is_string($labels) ? [$labels] : $labels;

        $matches = u($value)->match(self::ARBITRARY_VARIABLE_REGEX);

        if ([] !== $matches) {
            if ('' !== $matches[1] && '0' !== $matches[1] && null !== $matches[1]) {
                return \in_array($matches[1], $labels);
            }

            return $shouldMatchNoLabel;
        }

        return false;
    }
}
