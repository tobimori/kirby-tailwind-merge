<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class AnyValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        return true;
    }
}
