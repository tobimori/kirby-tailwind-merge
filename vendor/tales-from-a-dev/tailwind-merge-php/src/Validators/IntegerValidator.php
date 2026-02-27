<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class IntegerValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        return self::isIntegerOnly($value);
    }

    private static function isIntegerOnly(string $value): bool
    {
        return (string) (int) $value === $value;
    }
}
