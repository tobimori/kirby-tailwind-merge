<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class NamedContainerQueryValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        if (!str_starts_with($value, '@container')) {
            return false;
        }

        $rest = substr($value, 10);

        return (str_starts_with($rest, '/') && isset($rest[1]))
            || (str_starts_with($rest, '-size/') && isset($rest[6]))
            || (str_starts_with($rest, '-normal/') && isset($rest[8]));
    }
}
