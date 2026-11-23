<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class TshirtSizeValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        return 1 === preg_match(self::T_SHIRT_UNIT_REGEX, $value);
    }
}
