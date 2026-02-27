<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class ArbitraryValueNumberValidator implements ValidatorInterface
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, 'number', NumberValidator::validate(...));
    }
}
