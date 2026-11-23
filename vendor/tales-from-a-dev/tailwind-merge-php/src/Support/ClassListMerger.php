<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

/**
 * @internal
 *
 * @phpstan-import-type Configuration from Config
 */
final class ClassListMerger
{
    private const RESOLVED_CLASS_CACHE_LIMIT = 5000;

    private ClassNameParser $parser;

    private ClassGroupUtils $classGroupUtils;

    private SortModifiers $sortModifiers;

    /** @var array<string, true> */
    private array $postfixLookupClassGroupIds;

    /**
     * Class name => null when the class is always kept, otherwise its class id
     * and the ids it puts in conflict, both prefixed with its modifier id.
     *
     * @var array<string, array{string, list<string>}|null>
     */
    private array $resolvedClassCache = [];

    /**
     * The generation rotated out of the memo, still answering hits.
     *
     * A full memo stops admitting names, so a scan over more names than it
     * holds cannot churn it, and only rotates once as many misses again have
     * gone by: the working set has moved, and a long-running worker then
     * memoizes the class names it sees now, not the first ones it ever saw.
     *
     * @var array<string, array{string, list<string>}|null>
     */
    private array $previousResolvedClassCache = [];

    private int $missesSinceFull = 0;

    /**
     * @param Configuration $configuration
     * @param bool          $useDefaultClassMap Only when `theme` and `classGroups` are the defaults
     */
    public function __construct(array $configuration, bool $useDefaultClassMap = false)
    {
        $this->parser = new ClassNameParser($configuration['prefix']);
        $this->classGroupUtils = new ClassGroupUtils($configuration['theme'], $configuration['classGroups'], $configuration['conflictingClassGroups'], $configuration['conflictingClassGroupModifiers'], $useDefaultClassMap);
        $this->sortModifiers = new SortModifiers($configuration['orderSensitiveModifiers']);
        $this->postfixLookupClassGroupIds = array_fill_keys($configuration['postfixLookupClassGroups'], true);
    }

    public function merge(string $classList): string
    {
        $classGroupsInConflict = [];
        $classNames = preg_split('/\s+/', trim($classList), -1, \PREG_SPLIT_NO_EMPTY);

        if (false === $classNames || [] === $classNames) {
            return '';
        }

        // Joined once at the end: string concatenation here is quadratic.
        $keptClassNames = [];

        foreach (array_reverse($classNames) as $className) {
            // array_key_exists, not isset: a null entry is a cached "always keep".
            if (\array_key_exists($className, $this->resolvedClassCache)) {
                $resolvedClass = $this->resolvedClassCache[$className];
            } else {
                $resolvedClass = \array_key_exists($className, $this->previousResolvedClassCache)
                    ? $this->previousResolvedClassCache[$className]
                    : $this->resolve($className);

                if (\count($this->resolvedClassCache) < self::RESOLVED_CLASS_CACHE_LIMIT) {
                    $this->resolvedClassCache[$className] = $resolvedClass;
                } elseif (++$this->missesSinceFull >= self::RESOLVED_CLASS_CACHE_LIMIT) {
                    $this->previousResolvedClassCache = $this->resolvedClassCache;
                    $this->resolvedClassCache = [$className => $resolvedClass];
                    $this->missesSinceFull = 0;
                }
            }

            if (null === $resolvedClass) {
                $keptClassNames[] = $className;

                continue;
            }

            [$classId, $conflictingClassIds] = $resolvedClass;

            if (isset($classGroupsInConflict[$classId])) {
                continue;
            }

            $classGroupsInConflict[$classId] = true;

            foreach ($conflictingClassIds as $conflictingClassId) {
                $classGroupsInConflict[$conflictingClassId] = true;
            }

            $keptClassNames[] = $className;
        }

        return implode(' ', array_reverse($keptClassNames));
    }

    /**
     * @return array{string, list<string>}|null
     */
    private function resolve(string $className): ?array
    {
        $parsedClassName = $this->parser->parse($className);

        if ($parsedClassName->isExternal) {
            return null;
        }

        $modifiers = $parsedClassName->modifiers;
        $baseClassName = $parsedClassName->baseClassName;
        $maybePostfixModifierPosition = $parsedClassName->maybePostfixModifierPosition;

        $hasPostfixModifier = null !== $maybePostfixModifierPosition;

        if ($hasPostfixModifier) {
            // A byte offset: never slice by code points.
            $baseClassNameWithoutPostfix = substr($baseClassName, 0, $maybePostfixModifierPosition);
            $classGroupId = $this->classGroupUtils->getClassGroupId($baseClassNameWithoutPostfix);

            $classGroupIdWithPostfix = null;
            if (null !== $classGroupId && isset($this->postfixLookupClassGroupIds[$classGroupId])) {
                $classGroupIdWithPostfix = $this->classGroupUtils->getClassGroupId($baseClassName);
            }

            if (null !== $classGroupIdWithPostfix && $classGroupIdWithPostfix !== $classGroupId) {
                $classGroupId = $classGroupIdWithPostfix;
                $hasPostfixModifier = false;
            } elseif (null === $classGroupId) {
                $classGroupId = $this->classGroupUtils->getClassGroupId($baseClassName);
                if (null !== $classGroupId) {
                    $hasPostfixModifier = false;
                }
            }
        } else {
            $classGroupId = $this->classGroupUtils->getClassGroupId($baseClassName);
        }

        if (null === $classGroupId) {
            return null;
        }

        $variantModifier = match (\count($modifiers)) {
            0 => '',
            1 => $modifiers[0],
            default => implode(ClassNameParser::MODIFIER_SEPARATOR, $this->sortModifiers->sort($modifiers)),
        };

        $modifierId = $parsedClassName->hasImportantModifier ? $variantModifier.ClassNameParser::IMPORTANT_MODIFIER : $variantModifier;

        $conflictingClassIds = [];
        foreach ($this->classGroupUtils->getConflictingClassGroupIds($classGroupId, $hasPostfixModifier) as $group) {
            $conflictingClassIds[] = $modifierId.$group;
        }

        return [$modifierId.$classGroupId, $conflictingClassIds];
    }
}
