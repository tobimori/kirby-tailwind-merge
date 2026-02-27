<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\ValueObjects;

final class ClassPartObject
{
    /**
     * @param array<array-key, ClassPartObject>      $nextPart
     * @param array<array-key, ClassValidatorObject> $validators
     */
    public function __construct(
        public array $nextPart = [],
        public array $validators = [],
        public ?string $classGroupId = null,
    ) {
    }
}
