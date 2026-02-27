<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Helper\Collection;

use function Symfony\Component\String\u;

/**
 * @internal
 */
final class LengthValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        if (NumberValidator::validate($value)) {
            return true;
        }

        if (self::stringLengths()->contains($value)) {
            return true;
        }

        return [] !== u($value)->match(self::FRACTION_REGEX);
    }

    /**
     * @return Collection<int, string>
     */
    private static function stringLengths(): Collection
    {
        return Collection::make(['px', 'full', 'screen']);
    }
}
