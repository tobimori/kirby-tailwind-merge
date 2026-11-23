<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class LengthValidator implements ValidatorInterface
{
    private const STRING_LENGTHS = ['px' => true, 'full' => true, 'screen' => true];

    public static function validate(string $value): bool
    {
        if (NumberValidator::validate($value)) {
            return true;
        }

        if (isset(self::STRING_LENGTHS[$value])) {
            return true;
        }

        return 1 === preg_match(self::FRACTION_REGEX, $value);
    }
}
