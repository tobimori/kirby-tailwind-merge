<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

use TalesFromADev\TailwindMerge\Helper\Collection;
use TalesFromADev\TailwindMerge\ValueObjects\ClassPartObject;
use TalesFromADev\TailwindMerge\ValueObjects\ClassValidatorObject;

use function Symfony\Component\String\u;

/**
 * @internal
 */
final class ClassGroupUtils
{
    private const CLASS_PART_SEPARATOR = '-';

    // I use two dots here because one dot is used as prefix for class groups in plugins
    private const ARBITRARY_PROPERTY_PREFIX = 'arbitrary..';

    private ClassMap $classMap;

    /**
     * @param array<string, list<mixed>>        $theme
     * @param array<string, list<mixed>>        $classGroups
     * @param array<string, array<int, string>> $conflictingClassGroups
     * @param array<string, array<int, string>> $conflictingClassGroupModifiers
     */
    public function __construct(
        private readonly array $theme,
        private readonly array $classGroups,
        private readonly array $conflictingClassGroups,
        private readonly array $conflictingClassGroupModifiers,
    ) {
        $this->classMap = new ClassMap();
    }

    public function getClassGroupId(string $class): ?string
    {
        if (str_starts_with($class, '[') && str_ends_with($class, ']')) {
            return $this->getGroupIdForArbitraryProperty($class);
        }

        $classParts = explode(self::CLASS_PART_SEPARATOR, $class);
        // Classes like `-inset-1` produce an empty string as first classPart. We assume that classes for negative values are used correctly and skip it.
        $startIndex = '' === $classParts[0] && \count($classParts) > 1 ? 1 : 0;
        $classPartObject = $this->classMap->processClassGroup($this->classGroups, $this->theme);

        return $this->getGroupRecursive($classParts, $startIndex, $classPartObject);
    }

    /**
     * @param array<array-key, string> $classParts
     */
    public function getGroupRecursive(array $classParts, int $startIndex, ClassPartObject $classPartObject): ?string
    {
        $classPathsLength = \count($classParts) - $startIndex;

        if (0 === $classPathsLength) {
            return $classPartObject->classGroupId;
        }

        $currentClassPart = $classParts[$startIndex] ?? null;
        $nextClassPartObject = $classPartObject->nextPart[$currentClassPart] ?? null;

        $classGroupFromNextClassPart = null !== $nextClassPartObject
            ? $this->getGroupRecursive($classParts, $startIndex + 1, $nextClassPartObject)
            : null
        ;

        if ($classGroupFromNextClassPart) {
            return $classGroupFromNextClassPart;
        }

        if ([] === $classPartObject->validators) {
            return null;
        }

        $classRest = 0 === $startIndex
            ? implode(self::CLASS_PART_SEPARATOR, $classParts)
            : implode(self::CLASS_PART_SEPARATOR, \array_slice($classParts, $startIndex))
        ;

        return Collection::make($classPartObject->validators)->first(static fn (ClassValidatorObject $validator) => ($validator->validator)($classRest))?->classGroupId;
    }

    /**
     * Get the class group ID for an arbitrary property.
     */
    public function getGroupIdForArbitraryProperty(string $className): ?string
    {
        if (-1 === u($className)->slice(1, -1)->indexOf(':')) {
            return null;
        }

        $content = u($className)->slice(1, -1);
        $colonIndex = $content->indexOf(':');
        $property = $content->slice(0, $colonIndex)->toString();

        if ('' !== $property && '0' !== $property) {
            return self::ARBITRARY_PROPERTY_PREFIX.$property;
        }

        return null;
    }

    /**
     * @return array<array-key, string>
     */
    public function getConflictingClassGroupIds(string $classGroupId, bool $hasPostfixModifier): array
    {
        $modifierConflicts = $this->conflictingClassGroupModifiers[$classGroupId] ?? [];
        $baseConflicts = $this->conflictingClassGroups[$classGroupId] ?? [];

        if ($hasPostfixModifier && [] !== $modifierConflicts) {
            return [...$baseConflicts, ...$modifierConflicts];
        }

        return $baseConflicts;
    }
}
