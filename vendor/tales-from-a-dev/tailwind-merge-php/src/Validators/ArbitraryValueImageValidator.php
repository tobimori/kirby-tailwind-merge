<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use function Symfony\Component\String\u;

/**
 * @internal
 */
final class ArbitraryValueImageValidator implements ValidatorInterface
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, ['image', 'url'], self::isImage(...));
    }

    private static function isImage(string $value): bool
    {
        return [] !== u($value)->match(self::IMAGE_REGEX);
    }
}
