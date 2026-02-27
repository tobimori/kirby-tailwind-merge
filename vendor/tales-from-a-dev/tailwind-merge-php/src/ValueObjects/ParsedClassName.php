<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\ValueObjects;

class ParsedClassName
{
    /**
     * @param array<array-key, string> $modifiers
     */
    public function __construct(
        public array $modifiers,
        public bool $hasImportantModifier,
        public string $baseClassName,
        public ?int $maybePostfixModifierPosition,
        public bool $isExternal = false,
    ) {
    }
}
