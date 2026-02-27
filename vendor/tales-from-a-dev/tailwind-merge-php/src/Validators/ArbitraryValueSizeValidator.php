<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class ArbitraryValueSizeValidator implements ValidatorInterface
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, ['length', 'size', 'bg-size'], static fn (): bool => false);
    }
}
