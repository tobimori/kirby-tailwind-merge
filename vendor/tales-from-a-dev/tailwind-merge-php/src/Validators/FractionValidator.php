<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

final class FractionValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        return true == preg_match(self::FRACTION_REGEX, $value);
    }
}
