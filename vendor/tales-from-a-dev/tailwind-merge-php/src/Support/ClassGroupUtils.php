<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

/**
 * @internal
 *
 * @phpstan-import-type CompiledClassMap from ClassMapCompiler
 */
final class ClassGroupUtils
{
    // Two dots, because plugins use one as their class group prefix.
    private const ARBITRARY_PROPERTY_PREFIX = 'arbitrary..';

    /**
     * Bounds the memo for long-running workers merging generated class names.
     */
    private const CLASS_GROUP_ID_CACHE_LIMIT = 5000;

    /**
     * @var CompiledClassMap|null
     */
    private ?array $compiledClassMap = null;

    /**
     * @var array<string, ?string>
     */
    private array $classGroupIdCache = [];

    /**
     * Rotated out of the memo, with the same admission rule as ClassListMerger.
     *
     * @var array<string, ?string>
     */
    private array $previousClassGroupIdCache = [];

    private int $missesSinceFull = 0;

    /**
     * @param array<string, list<mixed>>        $theme
     * @param array<string, list<mixed>>        $classGroups
     * @param array<string, array<int, string>> $conflictingClassGroups
     * @param array<string, array<int, string>> $conflictingClassGroupModifiers
     * @param bool                              $useDefaultClassMap             Only when `theme` and `classGroups` are the defaults
     */
    public function __construct(
        private readonly array $theme,
        private readonly array $classGroups,
        private readonly array $conflictingClassGroups,
        private readonly array $conflictingClassGroupModifiers,
        bool $useDefaultClassMap = false,
    ) {
        if ($useDefaultClassMap) {
            // Constant arrays: opcache serves them from shared memory, nothing is built.
            $this->compiledClassMap = ['literals' => DefaultClassMap::LITERALS, 'validators' => DefaultClassMap::VALIDATORS];
        }
    }

    public function getClassGroupId(string $class): ?string
    {
        // array_key_exists, not isset: null ("no class group") is cached too.
        if (\array_key_exists($class, $this->classGroupIdCache)) {
            return $this->classGroupIdCache[$class];
        }

        $classGroupId = \array_key_exists($class, $this->previousClassGroupIdCache)
            ? $this->previousClassGroupIdCache[$class]
            : $this->resolveClassGroupId($class);

        if (\count($this->classGroupIdCache) < self::CLASS_GROUP_ID_CACHE_LIMIT) {
            $this->classGroupIdCache[$class] = $classGroupId;
        } elseif (++$this->missesSinceFull >= self::CLASS_GROUP_ID_CACHE_LIMIT) {
            $this->previousClassGroupIdCache = $this->classGroupIdCache;
            $this->classGroupIdCache = [$class => $classGroupId];
            $this->missesSinceFull = 0;
        }

        return $classGroupId;
    }

    private function resolveClassGroupId(string $class): ?string
    {
        if (str_starts_with($class, '[') && str_ends_with($class, ']')) {
            return $this->getGroupIdForArbitraryProperty($class);
        }

        // Negative values like `-inset-1` start with an empty part.
        if (str_starts_with($class, ClassMap::CLASS_PART_SEPARATOR)) {
            $class = substr($class, 1);
        }

        $compiledClassMap = $this->compiledClassMap ??= ClassMapCompiler::compile($this->classGroups, $this->theme);

        if (isset($compiledClassMap['literals'][$class])) {
            return $compiledClassMap['literals'][$class];
        }

        // Validators of the deepest matching trie node first, as upstream's walk.
        $separatorPositions = [];
        for ($position = strpos($class, ClassMap::CLASS_PART_SEPARATOR); false !== $position; $position = strpos($class, ClassMap::CLASS_PART_SEPARATOR, $position + 1)) {
            $separatorPositions[] = $position;
        }

        for ($index = \count($separatorPositions) - 1; $index >= -1; --$index) {
            $restStart = $index >= 0 ? $separatorPositions[$index] + 1 : 0;
            $validators = $compiledClassMap['validators'][substr($class, 0, $restStart)] ?? null;

            if (null === $validators) {
                continue;
            }

            $classRest = substr($class, $restStart);

            foreach ($validators as [$validator, $classGroupId]) {
                if (\is_string($validator) ? $validator::validate($classRest) : $validator($classRest)) {
                    return $classGroupId;
                }
            }
        }

        return null;
    }

    private function getGroupIdForArbitraryProperty(string $className): ?string
    {
        $content = substr($className, 1, -1);
        $colonIndex = strpos($content, ':');

        if (false === $colonIndex) {
            return null;
        }

        $property = substr($content, 0, $colonIndex);

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
