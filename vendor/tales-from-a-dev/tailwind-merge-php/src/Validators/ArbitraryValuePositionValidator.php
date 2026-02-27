<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class ArbitraryValuePositionValidator implements ValidatorInterface
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, ['position', 'percentage'], static fn (): bool => false);
    }
}
