<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class PercentValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        if (!str_ends_with($value, '%')) {
            return false;
        }

        return NumberValidator::validate(substr($value, 0, -1));
    }
}
