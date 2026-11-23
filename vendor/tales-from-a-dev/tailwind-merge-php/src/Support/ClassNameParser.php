<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

use TalesFromADev\TailwindMerge\ValueObjects\ParsedClassName;

/**
 * @internal
 */
final class ClassNameParser
{
    public const MODIFIER_SEPARATOR = ':';

    public const IMPORTANT_MODIFIER = '!';

    public const EMPTY_MODIFIERS = [];

    private const SIGNIFICANT_CHARACTERS = ':/[]()';

    public function __construct(
        private readonly ?string $prefix = null,
    ) {
    }

    public function parse(string $className): ParsedClassName
    {
        if (null !== $this->prefix && '' !== $this->prefix) {
            $fullPrefix = $this->prefix.self::MODIFIER_SEPARATOR;

            if (!str_starts_with($className, $fullPrefix)) {
                return new ParsedClassName(
                    modifiers: self::EMPTY_MODIFIERS,
                    hasImportantModifier: false,
                    baseClassName: $className,
                    maybePostfixModifierPosition: null,
                    isExternal: true
                );
            }

            $className = substr($className, \strlen($fullPrefix));
        }

        $modifiers = [];

        $parentDepth = 0;
        $bracketDepth = 0;
        $modifierStart = 0;
        $postfixModifierPosition = null;

        $length = \strlen($className);

        for ($index = strcspn($className, self::SIGNIFICANT_CHARACTERS); $index < $length; $index += 1 + strcspn($className, self::SIGNIFICANT_CHARACTERS, $index + 1)) {
            $currentCharacter = $className[$index];

            if (0 === $bracketDepth && 0 === $parentDepth) {
                if (self::MODIFIER_SEPARATOR === $currentCharacter) {
                    $modifiers[] = substr($className, $modifierStart, $index - $modifierStart);
                    $modifierStart = $index + 1;

                    continue;
                }

                if ('/' === $currentCharacter) {
                    $postfixModifierPosition = $index;

                    continue;
                }
            }

            if ('[' === $currentCharacter) {
                ++$bracketDepth;
            } elseif (']' === $currentCharacter) {
                --$bracketDepth;
            } elseif ('(' === $currentCharacter) {
                ++$parentDepth;
            } elseif (')' === $currentCharacter) {
                --$parentDepth;
            }
        }

        $baseClassNameWithImportantModifier = [] === $modifiers ? $className : substr($className, $modifierStart);

        // The trailing `!` wins over the legacy leading one: `!p-2!` strips only the suffix.
        $hasTrailingImportantModifier = str_ends_with($baseClassNameWithImportantModifier, self::IMPORTANT_MODIFIER);
        $hasLeadingImportantModifier = !$hasTrailingImportantModifier && str_starts_with($baseClassNameWithImportantModifier, self::IMPORTANT_MODIFIER);

        if ($hasTrailingImportantModifier) {
            $baseClassName = substr($baseClassNameWithImportantModifier, 0, -1);
        } elseif ($hasLeadingImportantModifier) {
            $baseClassName = substr($baseClassNameWithImportantModifier, 1);
        } else {
            $baseClassName = $baseClassNameWithImportantModifier;
        }

        $hasImportantModifier = $hasTrailingImportantModifier || $hasLeadingImportantModifier;

        // Stripping a leading `!` shifts the postfix offset left by one.
        $maybePostfixModifierPosition = $postfixModifierPosition && $postfixModifierPosition > $modifierStart
            ? $postfixModifierPosition - $modifierStart - ($hasLeadingImportantModifier ? 1 : 0)
            : null
        ;

        return new ParsedClassName(
            modifiers: $modifiers,
            hasImportantModifier: $hasImportantModifier,
            baseClassName: $baseClassName,
            maybePostfixModifierPosition: $maybePostfixModifierPosition,
        );
    }
}
