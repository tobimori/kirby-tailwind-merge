<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class FractionValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        return 1 === preg_match(self::FRACTION_REGEX, $value);
    }
}
