<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use function Symfony\Component\String\u;

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
        return [] !== u($value)->match(self::LENGTH_UNIT_REGEX) && [] === u($value)->match(self::COLOR_FUNCTION_REGEX);
    }
}
