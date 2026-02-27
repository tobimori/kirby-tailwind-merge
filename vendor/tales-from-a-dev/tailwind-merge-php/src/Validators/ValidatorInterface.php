<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 *
 * @see https://github.com/dcastil/tailwind-merge/blob/main/src/lib/validators.ts
 */
interface ValidatorInterface
{
    final public const ARBITRARY_VALUE_REGEX = '/^\[(?:(\w[\w-]*):)?(.+)\]$/i';

    final public const ARBITRARY_VARIABLE_REGEX = '/^\((?:(\w[\w-]*):)?(.+)\)$/i';

    final public const FRACTION_REGEX = '/^\d+\/\d+$/';

    final public const T_SHIRT_UNIT_REGEX = '/^(\d+(\.\d+)?)?(xs|sm|md|lg|xl)$/';

    final public const LENGTH_UNIT_REGEX = '/\d+(%|px|r?em|[sdl]?v([hwib]|min|max)|pt|pc|in|cm|mm|cap|ch|ex|r?lh|cq(w|h|i|b|min|max))|\b(calc|min|max|clamp)\(.+\)|^0$/';

    final public const COLOR_FUNCTION_REGEX = '/^(rgba?|hsla?|hwb|(ok)?(lab|lch)|color-mix)\(.+\)$/';

    final public const IMAGE_REGEX = '/^(url|image|image-set|cross-fade|element|(repeating-)?(linear|radial|conic)-gradient)\(.+\)$/';

    final public const SHADOW_REGEX = '/^(inset_)?-?((\d+)?\.?(\d+)[a-z]+|0)_-?((\d+)?\.?(\d+)[a-z]+|0)/';

    public static function validate(string $value): bool;
}
