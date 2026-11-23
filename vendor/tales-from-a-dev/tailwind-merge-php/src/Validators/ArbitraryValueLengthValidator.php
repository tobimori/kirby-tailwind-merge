<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class ArbitraryValueLengthValidator implements ValidatorInterface
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, 'length', self::isLengthOnly(...));
    }

    private static function isLengthOnly(string $value): bool
    {
        return 1 === preg_match(self::LENGTH_UNIT_REGEX, $value)
            && 1 !== preg_match(self::COLOR_FUNCTION_REGEX, $value);
    }
}
