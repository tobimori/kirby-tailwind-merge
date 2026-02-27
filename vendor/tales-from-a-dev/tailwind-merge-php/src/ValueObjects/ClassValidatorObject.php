<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\ValueObjects;

final class ClassValidatorObject
{
    public function __construct(
        public string $classGroupId,
        public \Closure $validator,
    ) {
    }
}
