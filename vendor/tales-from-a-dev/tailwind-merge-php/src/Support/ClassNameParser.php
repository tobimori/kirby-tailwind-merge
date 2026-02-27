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

    public function __construct(
        private readonly ?string $prefix = null,
    ) {
    }

    public function parse(string $className): ParsedClassName
    {
        if ($this->prefix) {
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

        for ($index = 0; $index < \strlen($className); ++$index) {
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
        $baseClassName = $this->stripImportantModifier($baseClassNameWithImportantModifier);
        $hasImportantModifier = $baseClassName !== $baseClassNameWithImportantModifier;

        $maybePostfixModifierPosition = $postfixModifierPosition && $postfixModifierPosition > $modifierStart
            ? $postfixModifierPosition - $modifierStart
            : null
        ;

        return new ParsedClassName(
            modifiers: $modifiers,
            hasImportantModifier: $hasImportantModifier,
            baseClassName: $baseClassName,
            maybePostfixModifierPosition: $maybePostfixModifierPosition,
        );
    }

    private function stripImportantModifier(string $baseClassName): string
    {
        if (str_ends_with($baseClassName, self::IMPORTANT_MODIFIER)) {
            return substr($baseClassName, 0, -1);
        }

        // Legacy: important modifier at the start
        if (str_starts_with($baseClassName, self::IMPORTANT_MODIFIER)) {
            return substr($baseClassName, 1);
        }

        return $baseClassName;
    }
}
