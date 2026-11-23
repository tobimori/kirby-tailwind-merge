<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\ValueObjects;

/**
 * @internal
 */
final class ParsedClassName
{
    /**
     * @param array<array-key, string> $modifiers
     */
    public function __construct(
        public readonly array $modifiers,
        public readonly bool $hasImportantModifier,
        public readonly string $baseClassName,
        public readonly ?int $maybePostfixModifierPosition,
        public readonly bool $isExternal = false,
    ) {
    }
}
