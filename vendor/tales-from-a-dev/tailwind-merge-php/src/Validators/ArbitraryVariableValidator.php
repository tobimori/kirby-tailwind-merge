<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class ArbitraryVariableValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        return '(' === ($value[0] ?? '') && 1 === preg_match(self::ARBITRARY_VARIABLE_REGEX, $value);
    }
}
