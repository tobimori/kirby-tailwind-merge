<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class AnyNonArbitraryValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        $firstCharacter = $value[0] ?? '';

        if ('[' === $firstCharacter) {
            return 1 !== preg_match(self::ARBITRARY_VALUE_REGEX, $value);
        }

        if ('(' === $firstCharacter) {
            return 1 !== preg_match(self::ARBITRARY_VARIABLE_REGEX, $value);
        }

        return true;
    }
}
