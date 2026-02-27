<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

use Symfony\Component\String\AbstractString;
use TalesFromADev\TailwindMerge\Helper\Collection;

use function Symfony\Component\String\u;

/**
 * @internal
 *
 * @phpstan-import-type Configuration from Config
 */
final class ClassListMerger
{
    private ClassNameParser $parser;

    private ClassGroupUtils $classGroupUtils;

    private SortModifiers $sortModifiers;

    /**
     * @param Configuration $configuration
     */
    public function __construct(array $configuration)
    {
        $this->parser = new ClassNameParser($configuration['prefix']);
        $this->classGroupUtils = new ClassGroupUtils($configuration['theme'], $configuration['classGroups'], $configuration['conflictingClassGroups'], $configuration['conflictingClassGroupModifiers']);
        $this->sortModifiers = new SortModifiers($configuration['orderSensitiveModifiers']);
    }

    public function merge(string $classList): string
    {
        $classGroupsInConflict = [];
        $classNames = Collection::make(u($classList)->trim()->split(' '))
            ->map(static fn (AbstractString $className): string => $className->toString())
            ->reverse()
            ->all();

        $result = '';

        foreach ($classNames as $className) {
            $originalClassName = $className;

            $parsedClassName = $this->parser->parse($className);

            $modifiers = $parsedClassName->modifiers;
            $hasImportantModifier = $parsedClassName->hasImportantModifier;
            $baseClassName = $parsedClassName->baseClassName;
            $isExternal = $parsedClassName->isExternal;
            $maybePostfixModifierPosition = $parsedClassName->maybePostfixModifierPosition;

            if ($isExternal) {
                $result = $this->formatResult($originalClassName, $result);

                continue;
            }

            $hasPostfixModifier = null !== $maybePostfixModifierPosition;
            $classGroupId = $this->classGroupUtils->getClassGroupId(
                $hasPostfixModifier
                    ? u($baseClassName)->slice(0, $maybePostfixModifierPosition)->toString()
                    : $baseClassName,
            );

            if (!$classGroupId) {
                if (!$hasPostfixModifier) {
                    $result = $this->formatResult($originalClassName, $result);

                    continue;
                }

                $classGroupId = $this->classGroupUtils->getClassGroupId($baseClassName);

                if (!$classGroupId) {
                    $result = $this->formatResult($originalClassName, $result);

                    continue;
                }

                $hasPostfixModifier = false;
            }

            $variantModifier = match (\count($modifiers)) {
                0 => '',
                1 => $modifiers[0],
                default => implode(ClassNameParser::MODIFIER_SEPARATOR, $this->sortModifiers->sort($modifiers)),
            };

            $modifierId = $hasImportantModifier ? $variantModifier.ClassNameParser::IMPORTANT_MODIFIER : $variantModifier;
            $classId = $modifierId.$classGroupId;

            if (\array_key_exists($classId, $classGroupsInConflict)) {
                continue;
            }

            $classGroupsInConflict[$classId] = true;

            foreach ($this->classGroupUtils->getConflictingClassGroupIds($classGroupId, $hasPostfixModifier) as $group) {
                $classGroupsInConflict[$modifierId.$group] = true;
            }

            $result = $this->formatResult($originalClassName, $result);
        }

        return $result;
    }

    public function formatResult(string $originalClassName, string $result): string
    {
        return u(' ')->join([$originalClassName, $result])->trim()->toString();
    }
}
