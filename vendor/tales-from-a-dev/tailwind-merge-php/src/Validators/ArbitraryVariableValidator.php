<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use function Symfony\Component\String\u;

final class ArbitraryVariableValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        return [] !== u($value)->match(self::ARBITRARY_VARIABLE_REGEX);
    }
}
