<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\ValueObjects;

/**
 * @internal
 */
final class ClassValidatorObject
{
    public function __construct(
        public readonly string $classGroupId,
        public readonly \Closure $validator,
    ) {
    }
}
