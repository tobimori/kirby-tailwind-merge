<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use function Symfony\Component\String\u;

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

        return NumberValidator::validate(u($value)->slice(0, -1)->toString());
    }
}
