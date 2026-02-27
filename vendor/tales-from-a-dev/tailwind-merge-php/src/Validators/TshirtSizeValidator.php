<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use function Symfony\Component\String\u;

/**
 * @internal
 */
final class TshirtSizeValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        return [] !== u($value)->match(self::T_SHIRT_UNIT_REGEX);
    }
}
