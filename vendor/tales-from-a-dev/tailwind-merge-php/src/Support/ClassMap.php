<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

use TalesFromADev\TailwindMerge\ValueObjects\ClassPartObject;
use TalesFromADev\TailwindMerge\ValueObjects\ClassValidatorObject;
use TalesFromADev\TailwindMerge\ValueObjects\ThemeGetter;

/**
 * @internal
 */
final class ClassMap
{
    final public const CLASS_PART_SEPARATOR = '-';

    /**
     * @param array<string, list<mixed>> $classGroups
     * @param array<string, list<mixed>> $theme
     */
    public function processClassGroup(array $classGroups, array $theme): ClassPartObject
    {
        $classPartObject = new ClassPartObject();

        foreach ($classGroups as $classGroupId => $classGroup) {
            $this->processClassesRecursively($classGroup, $classPartObject, $classGroupId, $theme);
        }

        return $classPartObject;
    }

    /**
     * @param list<mixed>                $classGroup
     * @param array<string, list<mixed>> $theme
     */
    public function processClassesRecursively(array $classGroup, ClassPartObject $classPartObject, string $classGroupId, array $theme): void
    {
        foreach ($classGroup as $classDefinition) {
            $this->processClassDefinition($classDefinition, $classPartObject, $classGroupId, $theme);
        }
    }

    /**
     * @param array<string, list<mixed>> $theme
     */
    public function processClassDefinition(string|array|callable|ThemeGetter $classDefinition, ClassPartObject $classPartObject, string $classGroupId, array $theme): void
    {
        if (\is_string($classDefinition)) {
            $this->processStringDefinition($classDefinition, $classPartObject, $classGroupId);

            return;
        }

        if ($classDefinition instanceof ThemeGetter) {
            $this->processClassesRecursively($classDefinition->get($theme), $classPartObject, $classGroupId, $theme);

            return;
        }

        if (\is_callable($classDefinition)) {
            $this->processFunctionDefinition($classDefinition, $classPartObject, $classGroupId);

            return;
        }

        $this->processObjectDefinition($classDefinition, $classPartObject, $classGroupId, $theme);
    }

    public function processStringDefinition(string $classDefinition, ClassPartObject $classPartObject, string $classGroupId): void
    {
        $classPartObjectToEdit = '' === $classDefinition ? $classPartObject : $this->getPart($classPartObject, $classDefinition);
        $classPartObjectToEdit->classGroupId = $classGroupId;
    }

    public function processFunctionDefinition(callable $classDefinition, ClassPartObject $classPartObject, string $classGroupId): void
    {
        $classPartObject->validators[] = new ClassValidatorObject(
            classGroupId: $classGroupId,
            validator: $classDefinition,
        );
    }

    /**
     * @param array<string, list<mixed>> $classDefinition
     * @param array<string, list<mixed>> $theme
     */
    public function processObjectDefinition(array $classDefinition, ClassPartObject $classPartObject, string $classGroupId, array $theme): void
    {
        foreach ($classDefinition as $key => $classGroup) {
            $this->processClassesRecursively(
                $classGroup,
                self::getPart($classPartObject, $key),
                $classGroupId,
                $theme,
            );
        }
    }

    public function getPart(ClassPartObject $classPartObject, string $path): ClassPartObject
    {
        $currentClassPartObject = $classPartObject;

        foreach (explode(self::CLASS_PART_SEPARATOR, $path) as $pathPart) {
            if (!isset($currentClassPartObject->nextPart[$pathPart])) {
                $currentClassPartObject->nextPart[$pathPart] = new ClassPartObject();
            }

            $currentClassPartObject = $currentClassPartObject->nextPart[$pathPart];
        }

        return $currentClassPartObject;
    }
}
