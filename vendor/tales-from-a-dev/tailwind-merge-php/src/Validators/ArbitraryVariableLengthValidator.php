<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

final class ArbitraryVariableLengthValidator implements ValidatorInterface
{
    use ValidateArbitraryVariable;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryVariable($value, 'length');
    }
}
