<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use function Symfony\Component\String\u;

/**
 * @internal
 */
final class ArbitraryValueShadowValidator implements ValidatorInterface
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, 'shadow', self::isShadow(...));
    }

    private static function isShadow(string $value): bool
    {
        return [] !== u($value)->match(self::SHADOW_REGEX);
    }
}
