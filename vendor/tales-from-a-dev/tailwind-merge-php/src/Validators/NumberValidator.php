<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class NumberValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        return is_numeric($value);
    }
}
