<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

final class ArbitraryValueWeightValidator implements ValidatorInterface
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, ['number', 'weight'], static fn (): bool => true);
    }
}
