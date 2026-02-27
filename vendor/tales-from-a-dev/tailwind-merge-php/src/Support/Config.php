<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

use TalesFromADev\TailwindMerge\Validators\AnyNonArbitraryValidator;
use TalesFromADev\TailwindMerge\Validators\AnyValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueFamilyNameValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueImageValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueLengthValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueNumberValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValuePositionValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueShadowValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueSizeValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueWeightValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableFamilyNameValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableImageValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableLengthValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariablePositionValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableShadowValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableSizeValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableValidator;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableWeightValidator;
use TalesFromADev\TailwindMerge\Validators\FractionValidator;
use TalesFromADev\TailwindMerge\Validators\IntegerValidator;
use TalesFromADev\TailwindMerge\Validators\NumberValidator;
use TalesFromADev\TailwindMerge\Validators\PercentValidator;
use TalesFromADev\TailwindMerge\Validators\TshirtSizeValidator;
use TalesFromADev\TailwindMerge\ValueObjects\ThemeGetter;

/**
 * @phpstan-type Configuration array{
 *       cacheSize: int,
 *       prefix: ?string,
 *       theme: array<string, list<mixed>>,
 *       classGroups: array<string, list<mixed>>,
 *       conflictingClassGroups: array<string, list<string>>,
 *       conflictingClassGroupModifiers: array<string, list<string>>,
 *       orderSensitiveModifiers: list<string>,
 *   }
 */
final class Config
{
    /**
     * @var Configuration|array<string, mixed>
     */
    private static array $additionalConfig = [];

    /**
     * @return Configuration
     */
    public static function getMergedConfig(): array
    {
        /** @var Configuration|null $config */
        static $config = null;
        static $lastAdditionalConfig = null;

        // Reset default config if additional config has changed
        if ($lastAdditionalConfig !== self::$additionalConfig) {
            $config = null;
            $lastAdditionalConfig = self::$additionalConfig;
        }

        $config ??= self::getDefaultConfig();

        foreach (self::$additionalConfig as $key => $additionalConfig) {
            $config[$key] = self::mergePropertyRecursively($config, $key, $additionalConfig);
        }

        return $config;
    }

    /**
     * @return Configuration
     */
    public static function getDefaultConfig(): array
    {
        $themeColor = self::fromTheme('color');
        $themeFont = self::fromTheme('font');
        $themeText = self::fromTheme('text');
        $themeFontWeight = self::fromTheme('font-weight');
        $themeTracking = self::fromTheme('tracking');
        $themeLeading = self::fromTheme('leading');
        $themeBreakPoint = self::fromTheme('breakpoint');
        $themeContainer = self::fromTheme('container');
        $themeSpacing = self::fromTheme('spacing');
        $themeRadius = self::fromTheme('radius');
        $themeShadow = self::fromTheme('shadow');
        $themeInsetShadow = self::fromTheme('inset-shadow');
        $themeTextShadow = self::fromTheme('text-shadow');
        $themeDropShadow = self::fromTheme('drop-shadow');
        $themeBlur = self::fromTheme('blur');
        $themePerspective = self::fromTheme('perspective');
        $themeAspect = self::fromTheme('aspect');
        $themeEase = self::fromTheme('ease');
        $themeAnimate = self::fromTheme('animate');

        return [
            'cacheSize' => 500,
            'prefix' => null,
            'theme' => [
                'animate' => ['spin', 'ping', 'pulse', 'bounce'],
                'aspect' => ['video'],
                'blur' => [TshirtSizeValidator::validate(...)],
                'breakpoint' => [TshirtSizeValidator::validate(...)],
                'color' => [AnyValidator::validate(...)],
                'container' => [TshirtSizeValidator::validate(...)],
                'drop-shadow' => [TshirtSizeValidator::validate(...)],
                'ease' => ['in', 'out', 'in-out'],
                'font' => [AnyNonArbitraryValidator::validate(...)],
                'font-weight' => [
                    'thin',
                    'extralight',
                    'light',
                    'normal',
                    'medium',
                    'semibold',
                    'bold',
                    'extrabold',
                    'black',
                ],
                'inset-shadow' => [TshirtSizeValidator::validate(...)],
                'leading' => ['none', 'tight', 'snug', 'normal', 'relaxed', 'loose'],
                'perspective' => ['dramatic', 'near', 'normal', 'midrange', 'distant', 'none'],
                'radius' => [TshirtSizeValidator::validate(...)],
                'shadow' => [TshirtSizeValidator::validate(...)],
                'spacing' => ['px', NumberValidator::validate(...)],
                'text' => [TshirtSizeValidator::validate(...)],
                'tracking' => ['tighter', 'tight', 'normal', 'wide', 'wider', 'widest'],
            ],
            'classGroups' => [
                // --------------
                // --- Layout ---
                // --------------

                /*
                 * Aspect Ratio
                 *
                 * @see https://tailwindcss.com/docs/aspect-ratio
                 */
                'aspect' => [
                    [
                        'aspect' => [
                            'auto',
                            'square',
                            FractionValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            $themeAspect,
                        ],
                    ],
                ],
                /*
                 * Container
                 *
                 * @see https://tailwindcss.com/docs/container
                 * @deprecated since Tailwind CSS v4.0.0
                 */
                'container' => ['container'],
                /*
                 * Columns
                 *
                 * @see https://tailwindcss.com/docs/columns
                 */
                'columns' => [
                    [
                        'columns' => [
                            NumberValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            $themeContainer,
                        ],
                    ],
                ],
                /*
                 * Break After
                 *
                 * @see https://tailwindcss.com/docs/break-after
                 */
                'break-after' => [['break-after' => self::scaleBreak()]],
                /*
                 * Break Before
                 *
                 * @see https://tailwindcss.com/docs/break-before
                 */
                'break-before' => [['break-before' => self::scaleBreak()]],
                /*
                 * Break Inside
                 *
                 * @see https://tailwindcss.com/docs/break-inside
                 */
                'break-inside' => [['break-inside' => ['auto', 'avoid', 'avoid-page', 'avoid-column']]],
                /*
                 * Box Decoration Break
                 *
                 * @see https://tailwindcss.com/docs/box-decoration-break
                 */
                'box-decoration' => [['box-decoration' => ['slice', 'clone']]],
                /*
                 * Box Sizing
                 *
                 * @see https://tailwindcss.com/docs/box-sizing
                 */
                'box' => [['box' => ['border', 'content']]],
                /*
                 * Display
                 *
                 * @see https://tailwindcss.com/docs/display
                 */
                'display' => [
                    'block',
                    'inline-block',
                    'inline',
                    'flex',
                    'inline-flex',
                    'table',
                    'inline-table',
                    'table-caption',
                    'table-cell',
                    'table-column',
                    'table-column-group',
                    'table-footer-group',
                    'table-header-group',
                    'table-row-group',
                    'table-row',
                    'flow-root',
                    'grid',
                    'inline-grid',
                    'contents',
                    'list-item',
                    'hidden',
                ],
                /*
                 * Screen Reader Only
                 *
                 * @see https://tailwindcss.com/docs/display#screen-reader-only
                 */
                'sr' => ['sr-only', 'not-sr-only'],
                /*
                 * Floats
                 *
                 * @see https://tailwindcss.com/docs/float
                 */
                'float' => [['float' => ['right', 'left', 'none', 'start', 'end']]],
                /*
                 * Clear
                 *
                 * @see https://tailwindcss.com/docs/clear
                 */
                'clear' => [['clear' => ['left', 'right', 'both', 'none', 'start', 'end']]],
                /*
                 * Isolation
                 *
                 * @see https://tailwindcss.com/docs/isolation
                 */
                'isolation' => ['isolate', 'isolation-auto'],
                /*
                 * Object Fit
                 *
                 * @see https://tailwindcss.com/docs/object-fit
                 */
                'object-fit' => [['object' => ['contain', 'cover', 'fill', 'none', 'scale-down']]],
                /*
                 * Object Position
                 *
                 * @see https://tailwindcss.com/docs/object-position
                 */
                'object-position' => [['object' => self::scalePositionWithArbitrary()]],
                /*
                 * Overflow
                 *
                 * @see https://tailwindcss.com/docs/overflow
                 */
                'overflow' => [['overflow' => self::scaleOverflow()]],
                /*
                 * Overflow X
                 *
                 * @see https://tailwindcss.com/docs/overflow
                 */
                'overflow-x' => [['overflow-x' => self::scaleOverflow()]],
                /*
                 * Overflow Y
                 *
                 * @see https://tailwindcss.com/docs/overflow
                 */
                'overflow-y' => [['overflow-y' => self::scaleOverflow()]],
                /*
                 * Overscroll Behavior
                 *
                 * @see https://tailwindcss.com/docs/overscroll-behavior
                 */
                'overscroll' => [['overscroll' => self::scaleOverscroll()]],
                /*
                 * Overscroll Behavior X
                 *
                 * @see https://tailwindcss.com/docs/overscroll-behavior
                 */
                'overscroll-x' => [['overscroll-x' => self::scaleOverscroll()]],
                /*
                 * Overscroll Behavior Y
                 *
                 * @see https://tailwindcss.com/docs/overscroll-behavior
                 */
                'overscroll-y' => [['overscroll-y' => self::scaleOverscroll()]],
                /*
                 * Position
                 *
                 * @see https://tailwindcss.com/docs/position
                 */
                'position' => ['static', 'fixed', 'absolute', 'relative', 'sticky'],
                /*
                 * Top / Right / Bottom / Left
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'inset' => [['inset' => self::scaleInset($themeSpacing)]],
                /*
                 * Right / Left
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'inset-x' => [['inset-x' => self::scaleInset($themeSpacing)]],
                /*
                 * Top / Bottom
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'inset-y' => [['inset-y' => self::scaleInset($themeSpacing)]],
                /*
                 * Start
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'start' => [['start' => self::scaleInset($themeSpacing)]],
                /*
                 * End
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'end' => [['end' => self::scaleInset($themeSpacing)]],
                /*
                 * Top
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'top' => [['top' => self::scaleInset($themeSpacing)]],
                /*
                 * Right
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'right' => [['right' => self::scaleInset($themeSpacing)]],
                /*
                 * Bottom
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'bottom' => [['bottom' => self::scaleInset($themeSpacing)]],
                /*
                 * Left
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'left' => [['left' => self::scaleInset($themeSpacing)]],
                /*
                 * Visibility
                 *
                 * @see https://tailwindcss.com/docs/visibility
                 */
                'visibility' => ['visible', 'invisible', 'collapse'],
                /*
                 * Z-Index
                 *
                 * @see https://tailwindcss.com/docs/z-index
                 */
                'z' => [
                    [
                        'z' => [
                            IntegerValidator::validate(...),
                            'auto',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],

                // ------------------------
                // --- Flexbox and Grid ---
                // ------------------------

                /*
                 * Flex Basis
                 *
                 * @see https://tailwindcss.com/docs/flex-basis
                 */
                'basis' => [
                    [
                        'basis' => [
                            FractionValidator::validate(...),
                            'full',
                            'auto',
                            $themeContainer,
                            ...self::scaleUnambiguousSpacing($themeSpacing),
                        ],
                    ],
                ],
                /*
                 * Flex Direction
                 *
                 * @see https://tailwindcss.com/docs/flex-direction
                 */
                'flex-direction' => [['flex' => ['row', 'row-reverse', 'col', 'col-reverse']]],
                /*
                 * Flex Wrap
                 *
                 * @see https://tailwindcss.com/docs/flex-wrap
                 */
                'flex-wrap' => [['flex' => ['nowrap', 'wrap', 'wrap-reverse']]],
                /*
                 * Flex
                 *
                 * @see https://tailwindcss.com/docs/flex
                 */
                'flex' => [
                    [
                        'flex' => [
                            NumberValidator::validate(...),
                            FractionValidator::validate(...),
                            'auto',
                            'initial',
                            'none',
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Flex Grow
                 *
                 * @see https://tailwindcss.com/docs/flex-grow
                 */
                'grow' => [
                    [
                        'grow' => [
                            '',
                            NumberValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Flex Shrink
                 *
                 * @see https://tailwindcss.com/docs/flex-shrink
                 */
                'shrink' => [
                    [
                        'shrink' => [
                            '',
                            NumberValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Order
                 *
                 * @see https://tailwindcss.com/docs/order
                 */
                'order' => [
                    [
                        'order' => [
                            IntegerValidator::validate(...),
                            'first',
                            'last',
                            'none',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Grid Template Columns
                 *
                 * @see https://tailwindcss.com/docs/grid-template-columns
                 */
                'grid-cols' => [['grid-cols' => self::scaleGridTemplateColsRows()]],
                /*
                 * Grid Column Start / End
                 *
                 * @see https://tailwindcss.com/docs/grid-column
                 */
                'col-start-end' => [['col' => self::scaleGridColRowStartAndEnd()]],
                /*
                 * Grid Column Start
                 *
                 * @see https://tailwindcss.com/docs/grid-column
                 */
                'col-start' => [['col-start' => self::scaleGridColRowStartOrEnd()]],
                /*
                 * Grid Column End
                 *
                 * @see https://tailwindcss.com/docs/grid-column
                 */
                'col-end' => [['col-end' => self::scaleGridColRowStartOrEnd()]],
                /*
                 * Grid Template Rows
                 *
                 * @see https://tailwindcss.com/docs/grid-template-rows
                 */
                'grid-rows' => [['grid-rows' => self::scaleGridTemplateColsRows()]],
                /*
                 * Grid Row Start / End
                 *
                 * @see https://tailwindcss.com/docs/grid-row
                 */
                'row-start-end' => [['row' => self::scaleGridColRowStartAndEnd()]],
                /*
                 * Grid Row Start
                 *
                 * @see https://tailwindcss.com/docs/grid-row
                 */
                'row-start' => [['row-start' => self::scaleGridColRowStartOrEnd()]],
                /*
                 * Grid Row End
                 *
                 * @see https://tailwindcss.com/docs/grid-row
                 */
                'row-end' => [['row-end' => self::scaleGridColRowStartOrEnd()]],
                /*
                 * Grid Auto Flow
                 *
                 * @see https://tailwindcss.com/docs/grid-auto-flow
                 */
                'grid-flow' => [['grid-flow' => ['row', 'col', 'dense', 'row-dense', 'col-dense']]],
                /*
                 * Grid Auto Columns
                 *
                 * @see https://tailwindcss.com/docs/grid-auto-columns
                 */
                'auto-cols' => [['auto-cols' => self::scaleGridAutoColsRows()]],
                /*
                 * Grid Auto Rows
                 *
                 * @see https://tailwindcss.com/docs/grid-auto-rows
                 */
                'auto-rows' => [['auto-rows' => self::scaleGridAutoColsRows()]],
                /*
                 * Gap
                 *
                 * @see https://tailwindcss.com/docs/gap
                 */
                'gap' => [['gap' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Gap X
                 *
                 * @see https://tailwindcss.com/docs/gap
                 */
                'gap-x' => [['gap-x' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Gap Y
                 *
                 * @see https://tailwindcss.com/docs/gap
                 */
                'gap-y' => [['gap-y' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Justify Content
                 *
                 * @see https://tailwindcss.com/docs/justify-content
                 */
                'justify-content' => [['justify' => [...self::scaleAlignPrimaryAxis(), 'normal']]],
                /*
                 * Justify Items
                 *
                 * @see https://tailwindcss.com/docs/justify-items
                 */
                'justify-items' => [['justify-items' => [...self::scaleAlignSecondaryAxis(), 'normal']]],
                /*
                 * Justify Self
                 *
                 * @see https://tailwindcss.com/docs/justify-self
                 */
                'justify-self' => [['justify-self' => ['auto', ...self::scaleAlignSecondaryAxis()]]],
                /*
                 * Align Content
                 *
                 * @see https://tailwindcss.com/docs/align-content
                 */
                'align-content' => [['content' => ['normal', ...self::scaleAlignPrimaryAxis()]]],
                /*
                 * Align Items
                 *
                 * @see https://tailwindcss.com/docs/align-items
                 */
                'align-items' => [['items' => [...self::scaleAlignSecondaryAxis(), ['baseline' => ['', 'last']]]]],
                /*
                 * Align Self
                 *
                 * @see https://tailwindcss.com/docs/align-self
                 */
                'align-self' => [['self' => ['auto', ...self::scaleAlignSecondaryAxis(), ['baseline' => ['', 'last']]]]],
                /*
                 * Place Content
                 *
                 * @see https://tailwindcss.com/docs/place-content
                 */
                'place-content' => [['place-content' => self::scaleAlignPrimaryAxis()]],
                /*
                 * Place Items
                 *
                 * @see https://tailwindcss.com/docs/place-items
                 */
                'place-items' => [['place-items' => [...self::scaleAlignSecondaryAxis(), 'baseline']]],
                /*
                 * Place Self
                 *
                 * @see https://tailwindcss.com/docs/place-self
                 */
                'place-self' => [['place-self' => ['auto', ...self::scaleAlignSecondaryAxis()]]],
                // Spacing
                /*
                 * Padding
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'p' => [['p' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Padding X
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'px' => [['px' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Padding Y
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'py' => [['py' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Padding Start
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'ps' => [['ps' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Padding End
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pe' => [['pe' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Padding Top
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pt' => [['pt' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Padding Right
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pr' => [['pr' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Padding Bottom
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pb' => [['pb' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Padding Left
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pl' => [['pl' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Margin
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'm' => [['m' => self::scaleMargin($themeSpacing)]],
                /*
                 * Margin X
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'mx' => [['mx' => self::scaleMargin($themeSpacing)]],
                /*
                 * Margin Y
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'my' => [['my' => self::scaleMargin($themeSpacing)]],
                /*
                 * Margin Start
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'ms' => [['ms' => self::scaleMargin($themeSpacing)]],
                /*
                 * Margin End
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'me' => [['me' => self::scaleMargin($themeSpacing)]],
                /*
                 * Margin Top
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'mt' => [['mt' => self::scaleMargin($themeSpacing)]],
                /*
                 * Margin Right
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'mr' => [['mr' => self::scaleMargin($themeSpacing)]],
                /*
                 * Margin Bottom
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'mb' => [['mb' => self::scaleMargin($themeSpacing)]],
                /*
                 * Margin Left
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'ml' => [['ml' => self::scaleMargin($themeSpacing)]],
                /*
                 * Space Between X
                 *
                 * @see https://tailwindcss.com/docs/margin#adding-space-between-children
                 */
                'space-x' => [['space-x' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Space Between X Reverse
                 *
                 * @see https://tailwindcss.com/docs/margin#adding-space-between-children
                 */
                'space-x-reverse' => ['space-x-reverse'],
                /*
                 * Space Between Y
                 *
                 * @see https://tailwindcss.com/docs/margin#adding-space-between-children
                 */
                'space-y' => [['space-y' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Space Between Y Reverse
                 *
                 * @see https://tailwindcss.com/docs/margin#adding-space-between-children
                 */
                'space-y-reverse' => ['space-y-reverse'],

                // --------------
                // --- Sizing ---
                // --------------

                /*
                 * Size
                 *
                 * @see https://tailwindcss.com/docs/width#setting-both-width-and-height
                 */
                'size' => [['size' => self::scaleSizing($themeSpacing)]],
                /*
                 * Width
                 *
                 * @see https://tailwindcss.com/docs/width
                 */
                'w' => [['w' => [$themeContainer, 'screen', ...self::scaleSizing($themeSpacing)]]],
                /*
                 * Min-Width
                 *
                 * @see https://tailwindcss.com/docs/min-width
                 */
                'min-w' => [
                    [
                        'min-w' => [
                            $themeContainer,
                            'screen',
                            /* Deprecated. @see https://github.com/tailwindlabs/tailwindcss.com/issues/2027#issuecomment-2620152757 */
                            'none',
                            ...self::scaleSizing($themeSpacing),
                        ],
                    ],
                ],
                /*
                 * Max-Width
                 *
                 * @see https://tailwindcss.com/docs/max-width
                 */
                'max-w' => [
                    [
                        'max-w' => [
                            $themeContainer,
                            'screen',
                            'none',
                            /* Deprecated since Tailwind CSS v4.0.0. @see https://github.com/tailwindlabs/tailwindcss.com/issues/2027#issuecomment-2620152757 */
                            'prose',
                            /* Deprecated since Tailwind CSS v4.0.0. @see https://github.com/tailwindlabs/tailwindcss.com/issues/2027#issuecomment-2620152757 */
                            ['screen' => [$themeBreakPoint]],
                            ...self::scaleSizing($themeSpacing),
                        ],
                    ],
                ],
                /*
                 * Height
                 *
                 * @see https://tailwindcss.com/docs/height
                 */
                'h' => [['h' => ['screen', 'lh', ...self::scaleSizing($themeSpacing)]]],
                /*
                 * Min-Height
                 *
                 * @see https://tailwindcss.com/docs/min-height
                 */
                'min-h' => [
                    ['min-h' => ['screen', 'lh', 'none', ...self::scaleSizing($themeSpacing)]],
                ],
                /*
                 * Max-Height
                 *
                 * @see https://tailwindcss.com/docs/max-height
                 */
                'max-h' => [
                    ['max-h' => ['screen', 'lh', ...self::scaleSizing($themeSpacing)]],
                ],
                // ------------------
                // --- Typography ---
                // ------------------

                /*
                 * Font Size
                 *
                 * @see https://tailwindcss.com/docs/font-size
                 */
                'font-size' => [
                    [
                        'text' => [
                            'base',
                            $themeText,
                            ArbitraryVariableLengthValidator::validate(...),
                            ArbitraryValueLengthValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Font Smoothing
                 *
                 * @see https://tailwindcss.com/docs/font-smoothing
                 */
                'font-smoothing' => ['antialiased', 'subpixel-antialiased'],
                /*
                 * Font Style
                 *
                 * @see https://tailwindcss.com/docs/font-style
                 */
                'font-style' => ['italic', 'not-italic'],
                /*
                 * Font Weight
                 *
                 * @see https://tailwindcss.com/docs/font-weight
                 */
                'font-weight' => [
                    [
                        'font' => [
                            $themeFontWeight,
                            ArbitraryVariableWeightValidator::validate(...),
                            ArbitraryValueWeightValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Font Stretch
                 *
                 * @see https://tailwindcss.com/docs/font-stretch
                 */
                'font-stretch' => [
                    [
                        'font-stretch' => [
                            'ultra-condensed',
                            'extra-condensed',
                            'condensed',
                            'semi-condensed',
                            'normal',
                            'semi-expanded',
                            'expanded',
                            'extra-expanded',
                            'ultra-expanded',
                            PercentValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Font Family
                 *
                 * @see https://tailwindcss.com/docs/font-family
                 */
                'font-family' => [
                    [
                        'font' => [
                            ArbitraryVariableFamilyNameValidator::validate(...),
                            ArbitraryValueFamilyNameValidator::validate(...),
                            $themeFont,
                        ],
                    ],
                ],
                /*
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-normal' => ['normal-nums'],
                /*
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-ordinal' => ['ordinal'],
                /*
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-slashed-zero' => ['slashed-zero'],
                /*
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-figure' => ['lining-nums', 'oldstyle-nums'],
                /*
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-spacing' => ['proportional-nums', 'tabular-nums'],
                /*
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-fraction' => ['diagonal-fractions', 'stacked-fractons'],
                /*
                 * Letter Spacing
                 *
                 * @see https://tailwindcss.com/docs/letter-spacing
                 */
                'tracking' => [
                    [
                        'tracking' => [
                            $themeTracking,
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Line Clamp
                 *
                 * @see https://tailwindcss.com/docs/line-clamp
                 */
                'line-clamp' => [
                    [
                        'line-clamp' => [
                            NumberValidator::validate(...),
                            'none',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueNumberValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Line Height
                 *
                 * @see https://tailwindcss.com/docs/line-height
                 */
                'leading' => [
                    [
                        'leading' => [
                            /* Deprecated since Tailwind CSS v4.0.0. @see https://github.com/tailwindlabs/tailwindcss.com/issues/2027#issuecomment-2620152757 */
                            $themeLeading,
                            ...self::scaleUnambiguousSpacing($themeSpacing),
                        ],
                    ],
                ],
                /*
                 * List Style Image
                 *
                 * @see https://tailwindcss.com/docs/list-style-image
                 */
                'list-image' => [
                    [
                        'list-image' => [
                            'none',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * List Style Position
                 *
                 * @see https://tailwindcss.com/docs/list-style-position
                 */
                'list-style-position' => [['list' => ['inside', 'outside']]],
                /*
                 * List Style Type
                 *
                 * @see https://tailwindcss.com/docs/list-style-type
                 */
                'list-style-type' => [['list' => ['disc', 'decimal', 'none', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /*
                 * Text Alignment
                 *
                 * @see https://tailwindcss.com/docs/text-align
                 */
                'text-alignment' => [['text' => ['left', 'center', 'right', 'justify', 'start', 'end']]],
                /*
                 * Placeholder Color
                 *
                 * @deprecated since Tailwind CSS v3.0.0
                 * @see https://tailwindcss.com/docs/placeholder-color
                 */
                'placeholder-color' => [['placeholder' => self::scaleColor($themeColor)]],
                /*
                 * Text Color
                 *
                 * @see https://tailwindcss.com/docs/text-color
                 */
                'text-color' => [['text' => self::scaleColor($themeColor)]],
                /*
                 * Text Decoration
                 *
                 * @see https://tailwindcss.com/docs/text-decoration
                 */
                'text-decoration' => ['underline', 'overline', 'line-through', 'no-underline'],
                /*
                 * Text Decoration Style
                 *
                 * @see https://tailwindcss.com/docs/text-decoration-style
                 */
                'text-decoration-style' => [['decoration' => [...self::scaleLineStyle(), 'wavy']]],
                /*
                 * Text Decoration Thickness
                 *
                 * @see https://tailwindcss.com/docs/text-decoration-thickness
                 */
                'text-decoration-thickness' => [
                    [
                        'decoration' => [
                            NumberValidator::validate(...),
                            'from-font',
                            'auto',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueLengthValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Text Decoration Color
                 *
                 * @see https://tailwindcss.com/docs/text-decoration-color
                 */
                'text-decoration-color' => [['decoration' => self::scaleColor($themeColor)]],
                /*
                 * Text Underline Offset
                 *
                 * @see https://tailwindcss.com/docs/text-underline-offset
                 */
                'underline-offset' => [
                    [
                        'underline-offset' => [
                            NumberValidator::validate(...),
                            'auto',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Text Transform
                 *
                 * @see https://tailwindcss.com/docs/text-transform
                 */
                'text-transform' => ['uppercase', 'lowercase', 'capitalize', 'normal-case'],
                /*
                 * Text Overflow
                 *
                 * @see https://tailwindcss.com/docs/text-overflow
                 */
                'text-overflow' => ['truncate', 'text-ellipsis', 'text-clip'],
                /*
                 * Text Wrap
                 *
                 * @see https://tailwindcss.com/docs/text-wrap
                 */
                'text-wrap' => [['text' => ['wrap', 'nowrap', 'balance', 'pretty']]],
                /*
                 * Text Indent
                 *
                 * @see https://tailwindcss.com/docs/text-indent
                 */
                'indent' => [['indent' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Vertical Alignment
                 *
                 * @see https://tailwindcss.com/docs/vertical-align
                 */
                'vertical-align' => [
                    [
                        'align' => [
                            'baseline',
                            'top',
                            'middle',
                            'bottom',
                            'text-top',
                            'text-bottom',
                            'sub',
                            'super',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Whitespace
                 *
                 * @see https://tailwindcss.com/docs/whitespace
                 */
                'whitespace' => [
                    ['whitespace' => ['normal', 'nowrap', 'pre', 'pre-line', 'pre-wrap', 'break-spaces']],
                ],
                /*
                 * Word Break
                 *
                 * @see https://tailwindcss.com/docs/word-break
                 */
                'break' => [['break' => ['normal', 'words', 'all', 'keep']]],
                /*
                 * Overflow Wrap
                 *
                 * @see https://tailwindcss.com/docs/overflow-wrap
                 */
                'wrap' => [['wrap' => ['break-word', 'anywhere', 'normal']]],
                /*
                 * Hyphens
                 *
                 * @see https://tailwindcss.com/docs/hyphens
                 */
                'hyphens' => [['hyphens' => ['none', 'manual', 'auto']]],
                /*
                 * Content
                 *
                 * @see https://tailwindcss.com/docs/content
                 */
                'content' => [
                    [
                        'content' => [
                            'none',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],

                // -------------------
                // --- Backgrounds ---
                // -------------------

                /*
                 * Background Attachment
                 *
                 * @see https://tailwindcss.com/docs/background-attachment
                 */
                'bg-attachment' => [['bg' => ['fixed', 'local', 'scroll']]],
                /*
                 * Background Clip
                 *
                 * @see https://tailwindcss.com/docs/background-clip
                 */
                'bg-clip' => [['bg-clip' => ['border', 'padding', 'content', 'text']]],
                /*
                 * Background Origin
                 *
                 * @see https://tailwindcss.com/docs/background-origin
                 */
                'bg-origin' => [['bg-origin' => ['border', 'padding', 'content']]],
                /*
                 * Background Position
                 *
                 * @see https://tailwindcss.com/docs/background-position
                 */
                'bg-position' => [['bg' => self::scaleBgPosition()]],
                /*
                 * Background Repeat
                 *
                 * @see https://tailwindcss.com/docs/background-repeat
                 */
                'bg-repeat' => [['bg' => self::scaleBgRepeat()]],
                /*
                 * Background Size
                 *
                 * @see https://tailwindcss.com/docs/background-size
                 */
                'bg-size' => [['bg' => self::scaleBgSize()]],
                /*
                 * Background Image
                 *
                 * @see https://tailwindcss.com/docs/background-image
                 */
                'bg-image' => [
                    [
                        'bg' => [
                            'none',
                            [
                                'linear' => [
                                    ['to' => ['t', 'tr', 'r', 'br', 'b', 'bl', 'l', 'tl']],
                                    IntegerValidator::validate(...),
                                    ArbitraryVariableValidator::validate(...),
                                    ArbitraryValueValidator::validate(...),
                                ],
                                'radial' => ['', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)],
                                'conic' => [IntegerValidator::validate(...), ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)],
                            ],
                            ArbitraryVariableImageValidator::validate(...),
                            ArbitraryValueImageValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Background Color
                 *
                 * @see https://tailwindcss.com/docs/background-color
                 */
                'bg-color' => [['bg' => self::scaleColor($themeColor)]],
                /*
                 * Gradient Color Stops From Position
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-from-pos' => [['from' => self::scaleGradientStopPosition()]],
                /*
                 * Gradient Color Stops Via Position
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-via-pos' => [['via' => self::scaleGradientStopPosition()]],
                /*
                 * Gradient Color Stops To Position
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-to-pos' => [['to' => self::scaleGradientStopPosition()]],
                /*
                 * Gradient Color Stops From
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-from' => [['from' => self::scaleColor($themeColor)]],
                /*
                 * Gradient Color Stops Via
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-via' => [['via' => self::scaleColor($themeColor)]],
                /*
                 * Gradient Color Stops To
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-to' => [['to' => self::scaleColor($themeColor)]],

                // ---------------
                // --- Borders ---
                // ---------------

                /*
                 * Border Radius
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded' => [['rounded' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Start
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-s' => [['rounded-s' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius End
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-e' => [['rounded-e' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Top
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-t' => [['rounded-t' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Right
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-r' => [['rounded-r' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Bottom
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-b' => [['rounded-b' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Left
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-l' => [['rounded-l' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Start Start
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-ss' => [['rounded-ss' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Start End
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-se' => [['rounded-se' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius End End
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-ee' => [['rounded-ee' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius End Start
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-es' => [['rounded-es' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Top Left
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-tl' => [['rounded-tl' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Top Right
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-tr' => [['rounded-tr' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Bottom Right
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-br' => [['rounded-br' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Radius Bottom Left
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-bl' => [['rounded-bl' => self::scaleRadius($themeRadius)]],
                /*
                 * Border Width
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w' => [['border' => self::scaleBorderWidth()]],
                /*
                 * Border Width X
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-x' => [['border-x' => self::scaleBorderWidth()]],
                /*
                 * Border Width Y
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-y' => [['border-y' => self::scaleBorderWidth()]],
                /*
                 * Border Width Start
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-s' => [['border-s' => self::scaleBorderWidth()]],
                /*
                 * Border Width End
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-e' => [['border-e' => self::scaleBorderWidth()]],
                /*
                 * Border Width Top
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-t' => [['border-t' => self::scaleBorderWidth()]],
                /*
                 * Border Width Right
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-r' => [['border-r' => self::scaleBorderWidth()]],
                /*
                 * Border Width Bottom
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-b' => [['border-b' => self::scaleBorderWidth()]],
                /*
                 * Border Width Left
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-l' => [['border-l' => self::scaleBorderWidth()]],
                /*
                 * Divide Width X
                 *
                 * @see https://tailwindcss.com/docs/border-width#between-children
                 */
                'divide-x' => [['divide-x' => self::scaleBorderWidth()]],
                /*
                 * Divide Width X Reverse
                 *
                 * @see https://tailwindcss.com/docs/border-width#between-children
                 */
                'divide-x-reverse' => ['divide-x-reverse'],
                /*
                 * Divide Width Y
                 *
                 * @see https://tailwindcss.com/docs/border-width#between-children
                 */
                'divide-y' => [['divide-y' => self::scaleBorderWidth()]],
                /*
                 * Divide Width Y Reverse
                 *
                 * @see https://tailwindcss.com/docs/border-width#between-children
                 */
                'divide-y-reverse' => ['divide-y-reverse'],
                /*
                 * Border Style
                 *
                 * @see https://tailwindcss.com/docs/border-style
                 */
                'border-style' => [['border' => [...self::scaleLineStyle(), 'hidden', 'none']]],
                /*
                 * Divide Style
                 *
                 * @see https://tailwindcss.com/docs/border-style#setting-the-divider-style
                 * */
                'divide-style' => [['divide' => [...self::scaleLineStyle(), 'hidden', 'none']]],
                /*
                 * Border Color
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color' => [
                    [
                        'border' => self::scaleColor($themeColor),
                    ],
                ],
                /*
                 * Border Color X
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-x' => [['border-x' => self::scaleColor($themeColor)]],
                /*
                 * Border Color Y
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-y' => [['border-y' => self::scaleColor($themeColor)]],
                /*
                 * Border Color S
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-s' => [['border-s' => self::scaleColor($themeColor)]],
                /*
                 * Border Color E
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-e' => [['border-e' => self::scaleColor($themeColor)]],
                /*
                 * Border Color Top
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-t' => [['border-t' => self::scaleColor($themeColor)]],
                /*
                 * Border Color Right
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-r' => [['border-r' => self::scaleColor($themeColor)]],
                /*
                 * Border Color Bottom
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-b' => [['border-b' => self::scaleColor($themeColor)]],
                /*
                 * Border Color Left
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-l' => [['border-l' => self::scaleColor($themeColor)]],
                /*
                 * Divide Color
                 *
                 * @see https://tailwindcss.com/docs/divide-color
                 */
                'divide-color' => [['divide' => self::scaleColor($themeColor)]],
                /*
                 * Outline Style
                 *
                 * @see https://tailwindcss.com/docs/outline-style
                 */
                'outline-style' => [['outline' => [...self::scaleLineStyle(), 'none', 'hidden']]],
                /*
                 * Outline Offset
                 *
                 * @see https://tailwindcss.com/docs/outline-offset
                 */
                'outline-offset' => [['outline-offset' => [NumberValidator::validate(...), ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /*
                 * Outline Width
                 *
                 * @see https://tailwindcss.com/docs/outline-width
                 */
                'outline-w' => [['outline' => [NumberValidator::validate(...), ArbitraryVariableValidator::validate(...), ArbitraryValueLengthValidator::validate(...)]]],
                /*
                 * Outline Color
                 *
                 * @see https://tailwindcss.com/docs/outline-color
                 */
                'outline-color' => [['outline' => self::scaleColor($themeColor)]],

                // ---------------
                // --- Effects ---
                // ---------------

                /*
                 * Box Shadow
                 *
                 * @see https://tailwindcss.com/docs/box-shadow
                 */
                'shadow' => [
                    [
                        'shadow' => [
                            // Deprecated since Tailwind CSS v4.0.0
                            '',
                            'none',
                            $themeShadow,
                            ArbitraryVariableShadowValidator::validate(...),
                            ArbitraryValueShadowValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Box Shadow Color
                 *
                 * @see https://tailwindcss.com/docs/box-shadow#setting-the-shadow-color
                 */
                'shadow-color' => [['shadow' => self::scaleColor($themeColor)]],
                /*
                 * Inset Box Shadow
                 *
                 * @see https://tailwindcss.com/docs/box-shadow#adding-an-inset-shadow
                 */
                'inset-shadow' => [['inset-shadow' => ['none', $themeInsetShadow, ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /*
                 * Inset Box Shadow Color
                 *
                 * @see https://tailwindcss.com/docs/box-shadow#setting-the-inset-shadow-color
                 */
                'inset-shadow-color' => [['inset-shadow' => self::scaleColor($themeColor)]],
                /*
                 * Ring Width
                 *
                 * @see https://tailwindcss.com/docs/box-shadow#adding-a-ring
                 */
                'ring-w' => [['ring' => self::scaleBorderWidth()]],
                /*
                 * Ring Width Inset
                 *
                 * @see https://v3.tailwindcss.com/docs/ring-width#inset-rings
                 * @deprecated since Tailwind CSS v4.0.0
                 * @see https://github.com/tailwindlabs/tailwindcss/blob/v4.0.0/packages/tailwindcss/src/utilities.ts#L4158
                 */
                'ring-w-inset' => ['ring-inset'],
                /*
                 * Ring Color
                 *
                 * @see https://tailwindcss.com/docs/box-shadow#setting-the-ring-color
                 */
                'ring-color' => [['ring' => self::scaleColor($themeColor)]],
                /*
                 * Ring Offset Width
                 *
                 * @see https://v3.tailwindcss.com/docs/ring-offset-width
                 * @deprecated since Tailwind CSS v4.0.0
                 * @see https://github.com/tailwindlabs/tailwindcss/blob/v4.0.0/packages/tailwindcss/src/utilities.ts#L4158
                 */
                'ring-offset-w' => [['ring-offset' => [NumberValidator::validate(...), ArbitraryValueLengthValidator::validate(...)]]],
                /*
                 * Ring Offset Color
                 *
                 * @see https://v3.tailwindcss.com/docs/ring-offset-color
                 * @deprecated since Tailwind CSS v4.0.0
                 * @see https://github.com/tailwindlabs/tailwindcss/blob/v4.0.0/packages/tailwindcss/src/utilities.ts#L4158
                 */
                'ring-offset-color' => [['ring-offset' => self::scaleColor($themeColor)]],
                /*
                 * Inset Ring Width
                 *
                 * @see https://tailwindcss.com/docs/box-shadow#adding-an-inset-ring
                 */
                'inset-ring-w' => [['inset-ring' => self::scaleBorderWidth()]],
                /*
                 * Inset Ring Color
                 *
                 * @see https://tailwindcss.com/docs/box-shadow#setting-the-inset-ring-color
                 */
                'inset-ring-color' => [['inset-ring' => self::scaleColor($themeColor)]],
                /*
                 * Text Shadow
                 *
                 * @see https://tailwindcss.com/docs/text-shadow
                 */
                'text-shadow' => [
                    [
                        'text-shadow' => [
                            'none',
                            $themeTextShadow,
                            ArbitraryVariableShadowValidator::validate(...),
                            ArbitraryValueShadowValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Text Shadow Color
                 *
                 * @see https://tailwindcss.com/docs/text-shadow#setting-the-shadow-color
                 */
                'text-shadow-color' => [['text-shadow' => self::scaleColor($themeColor)]],
                /*
                 * Opacity
                 *
                 * @see https://tailwindcss.com/docs/opacity
                 */
                'opacity' => [['opacity' => [NumberValidator::validate(...), ArbitraryValueValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /*
                 * Mix Blend Mode
                 *
                 * @see https://tailwindcss.com/docs/mix-blend-mode
                 */
                'mix-blend' => [['mix-blend' => [...self::scaleBlendMode(), 'plus-darker', 'plus-lighter']]],
                /*
                 * Background Blend Mode
                 *
                 * @see https://tailwindcss.com/docs/background-blend-mode
                 */
                'bg-blend' => [['bg-blend' => self::scaleBlendMode()]],
                /*
                 * Mask Clip
                 *
                 * @see https://tailwindcss.com/docs/mask-clip
                 */
                'mask-clip' => [
                    ['mask-clip' => ['border', 'padding', 'content', 'fill', 'stroke', 'view']],
                    'mask-no-clip',
                ],
                /*
                 * Mask Composite
                 *
                 * @see https://tailwindcss.com/docs/mask-composite
                 */
                'mask-composite' => [['mask' => ['add', 'subtract', 'intersect', 'exclude']]],
                /*
                 * Mask Image
                 *
                 * @see https://tailwindcss.com/docs/mask-image
                 */
                'mask-image-linear-pos' => [['mask-linear' => [NumberValidator::validate(...)]]],
                'mask-image-linear-from-pos' => [['mask-linear-from' => self::scaleMaskImagePosition()]],
                'mask-image-linear-to-pos' => [['mask-linear-to' => self::scaleMaskImagePosition()]],
                'mask-image-linear-from-color' => [['mask-linear-from' => self::scaleColor($themeColor)]],
                'mask-image-linear-to-color' => [['mask-linear-to' => self::scaleColor($themeColor)]],
                'mask-image-t-from-pos' => [['mask-t-from' => self::scaleMaskImagePosition()]],
                'mask-image-t-to-pos' => [['mask-t-to' => self::scaleMaskImagePosition()]],
                'mask-image-t-from-color' => [['mask-t-from' => self::scaleColor($themeColor)]],
                'mask-image-t-to-color' => [['mask-t-to' => self::scaleColor($themeColor)]],
                'mask-image-r-from-pos' => [['mask-r-from' => self::scaleMaskImagePosition()]],
                'mask-image-r-to-pos' => [['mask-r-to' => self::scaleMaskImagePosition()]],
                'mask-image-r-from-color' => [['mask-r-from' => self::scaleColor($themeColor)]],
                'mask-image-r-to-color' => [['mask-r-to' => self::scaleColor($themeColor)]],
                'mask-image-b-from-pos' => [['mask-b-from' => self::scaleMaskImagePosition()]],
                'mask-image-b-to-pos' => [['mask-b-to' => self::scaleMaskImagePosition()]],
                'mask-image-b-from-color' => [['mask-b-from' => self::scaleColor($themeColor)]],
                'mask-image-b-to-color' => [['mask-b-to' => self::scaleColor($themeColor)]],
                'mask-image-l-from-pos' => [['mask-l-from' => self::scaleMaskImagePosition()]],
                'mask-image-l-to-pos' => [['mask-l-to' => self::scaleMaskImagePosition()]],
                'mask-image-l-from-color' => [['mask-l-from' => self::scaleColor($themeColor)]],
                'mask-image-l-to-color' => [['mask-l-to' => self::scaleColor($themeColor)]],
                'mask-image-x-from-pos' => [['mask-x-from' => self::scaleMaskImagePosition()]],
                'mask-image-x-to-pos' => [['mask-x-to' => self::scaleMaskImagePosition()]],
                'mask-image-x-from-color' => [['mask-x-from' => self::scaleColor($themeColor)]],
                'mask-image-x-to-color' => [['mask-x-to' => self::scaleColor($themeColor)]],
                'mask-image-y-from-pos' => [['mask-y-from' => self::scaleMaskImagePosition()]],
                'mask-image-y-to-pos' => [['mask-y-to' => self::scaleMaskImagePosition()]],
                'mask-image-y-from-color' => [['mask-y-from' => self::scaleColor($themeColor)]],
                'mask-image-y-to-color' => [['mask-y-to' => self::scaleColor($themeColor)]],
                'mask-image-radial' => [['mask-radial' => [ArbitraryValueValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                'mask-image-radial-from-pos' => [['mask-radial-from' => self::scaleMaskImagePosition()]],
                'mask-image-radial-to-pos' => [['mask-radial-to' => self::scaleMaskImagePosition()]],
                'mask-image-radial-from-color' => [['mask-radial-from' => self::scaleColor($themeColor)]],
                'mask-image-radial-to-color' => [['mask-radial-to' => self::scaleColor($themeColor)]],
                'mask-image-radial-shape' => [['mask-radial' => ['circle', 'ellipse']]],
                'mask-image-radial-size' => [['mask-radial' => [['closest' => ['side', 'corner'], 'farthest' => ['side', 'corner']]]]],
                'mask-image-radial-pos' => [['mask-radial-at' => self::scalePosition()]],
                'mask-image-conic-pos' => [['mask-conic-at' => [NumberValidator::validate(...)]]],
                'mask-image-conic-from-pos' => [['mask-conic-from' => self::scaleMaskImagePosition()]],
                'mask-image-conic-to-pos' => [['mask-conic-to' => self::scaleMaskImagePosition()]],
                'mask-image-conic-from-color' => [['mask-conic-from' => self::scaleColor($themeColor)]],
                'mask-image-conic-to-color' => [['mask-conic-to' => self::scaleColor($themeColor)]],
                /*
                 * Mask Mode
                 *
                 * @see https://tailwindcss.com/docs/mask-mode
                 */
                'mask-mode' => [['mask' => ['alpha', 'luminance', 'match']]],
                /*
                 * Mask Origin
                 *
                 * @see https://tailwindcss.com/docs/mask-origin
                 */
                'mask-origin' => [['mask-origin' => ['border', 'padding', 'content', 'fill', 'stroke', 'view']]],
                /*
                 * Mask Position
                 *
                 * @see https://tailwindcss.com/docs/mask-position
                 */
                'mask-position' => [['mask' => self::scaleBgPosition()]],
                /*
                 * Mask Repeat
                 *
                 * @see https://tailwindcss.com/docs/mask-repeat
                 */
                'mask-repeat' => [['mask' => self::scaleBgRepeat()]],
                /*
                 * Mask Size
                 *
                 * @see https://tailwindcss.com/docs/mask-size
                 */
                'mask-size' => [['mask' => self::scaleBgSize()]],
                /*
                 * Mask Type
                 *
                 * @see https://tailwindcss.com/docs/mask-type
                 */
                'mask-type' => [['mask-type' => ['alpha', 'luminance']]],
                /*
                 * Mask Image
                 *
                 * @see https://tailwindcss.com/docs/mask-image
                 */
                'mask-image' => [['mask' => ['none', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],

                // ---------------
                // --- Filters ---
                // ---------------

                /*
                 * Filter
                 *
                 * @see https://tailwindcss.com/docs/filter
                 */
                'filter' => [[
                    'filter' => [
                        // Deprecated since Tailwind CSS v3.0.0
                        '',
                        'none',
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...),
                    ],
                ]],
                /*
                 * Blur
                 *
                 * @see https://tailwindcss.com/docs/blur
                 */
                'blur' => [['blur' => self::scaleBlur($themeBlur)]],
                /*
                 * Brightness
                 *
                 * @see https://tailwindcss.com/docs/brightness
                 */
                'brightness' => [
                    [
                        'brightness' => [
                            NumberValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Contrast
                 *
                 * @see https://tailwindcss.com/docs/contrast
                 */
                'contrast' => [
                    [
                        'contrast' => [
                            NumberValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Drop Shadow
                 *
                 * @see https://tailwindcss.com/docs/drop-shadow
                 */
                'drop-shadow' => [
                    [
                        'drop-shadow' => [
                            // Deprecated since Tailwind CSS v4.0.0
                            '',
                            'none',
                            $themeDropShadow,
                            ArbitraryVariableShadowValidator::validate(...),
                            ArbitraryValueShadowValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Drop Shadow Color
                 *
                 * @see https://tailwindcss.com/docs/filter-drop-shadow#setting-the-shadow-color
                 */
                'drop-shadow-color' => [['drop-shadow' => self::scaleColor($themeColor)]],
                /*
                 * Grayscale
                 *
                 * @see https://tailwindcss.com/docs/grayscale
                 */
                'grayscale' => [
                    [
                        'grayscale' => [
                            '',
                            NumberValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Hue Rotate
                 *
                 * @see https://tailwindcss.com/docs/hue-rotate
                 */
                'hue-rotate' => [
                    [
                        'hue-rotate' => [
                            NumberValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Invert
                 *
                 * @see https://tailwindcss.com/docs/invert
                 */
                'invert' => [['invert' => [
                    '',
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /*
                 * Saturate
                 *
                 * @see https://tailwindcss.com/docs/saturate
                 */
                'saturate' => [['saturate' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /*
                 * Sepia
                 *
                 * @see https://tailwindcss.com/docs/sepia
                 */
                'sepia' => [['sepia' => [
                    '',
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /*
                 * Backdrop Filter
                 *
                 * @see https://tailwindcss.com/docs/backdrop-filter
                 */
                'backdrop-filter' => [[
                    'backdrop-filter' => [
                        // @deprecated since Tailwind CSS v3.0.0
                        '',
                        'none',
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...),
                    ],
                ]],
                /*
                 * Backdrop Blur
                 *
                 * @see https://tailwindcss.com/docs/backdrop-blur
                 */
                'backdrop-blur' => [['backdrop-blur' => self::scaleBlur($themeBlur)]],
                /*
                 * Backdrop Brightness
                 *
                 * @see https://tailwindcss.com/docs/backdrop-brightness
                 */
                'backdrop-brightness' => [['backdrop-brightness' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Backdrop Contrast
                 *
                 * @see https://tailwindcss.com/docs/backdrop-contrast
                 */
                'backdrop-contrast' => [['backdrop-contrast' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Backdrop Grayscale
                 *
                 * @see https://tailwindcss.com/docs/backdrop-grayscale
                 */
                'backdrop-grayscale' => [['backdrop-grayscale' => [
                    '',
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Backdrop Hue Rotate
                 *
                 * @see https://tailwindcss.com/docs/backdrop-hue-rotate
                 */
                'backdrop-hue-rotate' => [['backdrop-hue-rotate' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Backdrop Invert
                 *
                 * @see https://tailwindcss.com/docs/backdrop-invert
                 */
                'backdrop-invert' => [['backdrop-invert' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Backdrop Opacity
                 *
                 * @see https://tailwindcss.com/docs/backdrop-opacity
                 */
                'backdrop-opacity' => [['backdrop-opacity' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Backdrop Saturate
                 *
                 * @see https://tailwindcss.com/docs/backdrop-saturate
                 */
                'backdrop-saturate' => [['backdrop-saturate' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Backdrop Sepia
                 *
                 * @see https://tailwindcss.com/docs/backdrop-sepia
                 */
                'backdrop-sepia' => [['backdrop-sepia' => [
                    '',
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],

                // --------------
                // --- Tables ---
                // --------------

                /*
                 * Border Collapse
                 *
                 * @see https://tailwindcss.com/docs/border-collapse
                 */
                'border-collapse' => [['border' => ['collapse', 'separate']]],
                /*
                 * Border Spacing
                 *
                 * @see https://tailwindcss.com/docs/border-spacing
                 */
                'border-spacing' => [['border-spacing' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Border Spacing X
                 *
                 * @see https://tailwindcss.com/docs/border-spacing
                 */
                'border-spacing-x' => [['border-spacing-x' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Border Spacing Y
                 *
                 * @see https://tailwindcss.com/docs/border-spacing
                 */
                'border-spacing-y' => [['border-spacing-y' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Table Layout
                 *
                 * @see https://tailwindcss.com/docs/table-layout
                 */
                'table-layout' => [['table' => ['auto', 'fixed']]],
                /*
                 * Caption Side
                 *
                 * @see https://tailwindcss.com/docs/caption-side
                 */
                'caption' => [['caption' => ['top', 'bottom']]],

                // ---------------------------------
                // --- Transitions and Animation ---
                // ---------------------------------

                /*
                 * Transition Property
                 *
                 * @see https://tailwindcss.com/docs/transition-property
                 */
                'transition' => [
                    [
                        'transition' => [
                            '',
                            'all',
                            'colors',
                            'opacity',
                            'shadow',
                            'transform',
                            'none',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Transition Behavior
                 *
                 * @see https://tailwindcss.com/docs/transition-behavior
                 */
                'transition-behavior' => [
                    [
                        'transition' => ['normal', 'discrete'],
                    ],
                ],
                /*
                 * Transition Duration
                 *
                 * @see https://tailwindcss.com/docs/transition-duration
                 */
                'duration' => [['duration' => [
                    NumberValidator::validate(...),
                    'initial',
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Transition Timing Function
                 *
                 * @see https://tailwindcss.com/docs/transition-timing-function
                 */
                'ease' => [['ease' => [
                    'linear',
                    'initial',
                    $themeEase,
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Transition Delay
                 *
                 * @see https://tailwindcss.com/docs/transition-delay
                 */
                'delay' => [['delay' => [
                    NumberValidator::validate(...),
                    'initial',
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Animation
                 *
                 * @see https://tailwindcss.com/docs/animation
                 */
                'animate' => [['animate' => [
                    'none',
                    $themeAnimate,
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],

                // ------------------
                // --- Transforms ---
                // ------------------

                /*
                 * Backface Visibility
                 *
                 * @see https://tailwindcss.com/docs/backface-visibility
                 */
                'backface' => [
                    [
                        'backface' => ['hidden', 'visible'],
                    ],
                ],
                /*
                 * Perspective
                 *
                 * @see https://tailwindcss.com/docs/perspective
                 */
                'perspective' => [['perspective' => [
                    $themePerspective,
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                ]],
                /*
                 * Perspective Origin
                 *
                 * @see https://tailwindcss.com/docs/perspective-origin
                 */
                'perspective-origin' => [['perspective-origin' => self::scaleOrigin()]],
                /*
                 * Rotate
                 *
                 * @see https://tailwindcss.com/docs/rotate
                 */
                'rotate' => [['rotate' => self::scaleRotate()]],
                'rotate-x' => [['rotate-x' => self::scaleRotate()]],
                'rotate-y' => [['rotate-y' => self::scaleRotate()]],
                'rotate-z' => [['rotate-z' => self::scaleRotate()]],
                /*
                 * Scale
                 *
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale' => [
                    [
                        'scale' => self::scaleScale(),
                    ],
                ],
                /*
                 * Scale X
                 *
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale-x' => [
                    [
                        'scale-x' => self::scaleScale(),
                    ],
                ],

                /*
                 * Scale Y
                 *
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale-y' => [
                    [
                        'scale-y' => self::scaleScale(),
                    ],
                ],

                /*
                 * Scale Z
                 *
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale-z' => [
                    [
                        'scale-z' => self::scaleScale(),
                    ],
                ],

                /*
                 * Scale 3D
                 *
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale-3d' => ['scale-3d'],
                /*
                 * Skew
                 *
                 * @see https://tailwindcss.com/docs/skew
                 */
                'skew' => [['skew' => self::scaleSkew($themeSpacing)]],
                /*
                 * Skew X
                 *
                 * @see https://tailwindcss.com/docs/skew
                 */
                'skew-x' => [['skew-x' => self::scaleSkew($themeSpacing)]],
                /*
                 * Skew Y
                 *
                 * @see https://tailwindcss.com/docs/skew
                 */
                'skew-y' => [['skew-y' => self::scaleSkew($themeSpacing)]],
                /*
                 * Transform
                 *
                 * @see https://tailwindcss.com/docs/transform
                 */
                'transform' => [
                    [
                        'transform' => [
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                            '',
                            'none',
                            'gpu',
                            'cpu',
                        ],
                    ],
                ],
                /*
                 * Transform Origin
                 *
                 * @see https://tailwindcss.com/docs/transform-origin
                 */
                'transform-origin' => [
                    [
                        'origin' => self::scalePositionWithArbitrary(),
                    ],
                ],
                /*
                 * Transform Style
                 *
                 * @see https://tailwindcss.com/docs/transform-style
                 */
                'transform-style' => [['transform' => ['3d', 'flat']]],
                /*
                 * Translate
                 *
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate' => [
                    [
                        'translate' => self::scaleTranslate($themeSpacing),
                    ],
                ],

                /*
                 * Translate X
                 *
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate-x' => [
                    [
                        'translate-x' => self::scaleTranslate($themeSpacing),
                    ],
                ],

                /*
                 * Translate Y
                 *
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate-y' => [
                    [
                        'translate-y' => self::scaleTranslate($themeSpacing),
                    ],
                ],

                /*
                 * Translate Z
                 *
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate-z' => [
                    [
                        'translate-z' => self::scaleTranslate($themeSpacing),
                    ],
                ],

                /*
                 * Translate None
                 *
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate-none' => ['translate-none'],

                // ---------------------
                // --- Interactivity ---
                // ---------------------

                /*
                 * Accent Color
                 *
                 * @see https://tailwindcss.com/docs/accent-color
                 */
                'accent' => [['accent' => self::scaleColor($themeColor)]],
                /*
                 * Appearance
                 *
                 * @see https://tailwindcss.com/docs/appearance
                 */
                'appearance' => [['appearance' => ['none', 'auto']]],
                /*
                 * Caret Color
                 *
                 * @see https://tailwindcss.com/docs/just-in-time-mode#caret-color-utilities
                 */
                'caret-color' => [
                    ['caret' => self::scaleColor($themeColor)],
                ],
                /*
                 * Color Scheme
                 *
                 * @see https://tailwindcss.com/docs/color-scheme
                 */
                'color-scheme' => [
                    ['scheme' => ['normal', 'dark', 'light', 'light-dark', 'only-dark', 'only-light']],
                ],
                /*
                 * Cursor
                 *
                 * @see https://tailwindcss.com/docs/cursor
                 */
                'cursor' => [
                    [
                        'cursor' => [
                            'auto',
                            'default',
                            'pointer',
                            'wait',
                            'text',
                            'move',
                            'help',
                            'not-allowed',
                            'none',
                            'context-menu',
                            'progress',
                            'cell',
                            'crosshair',
                            'vertical-text',
                            'alias',
                            'copy',
                            'no-drop',
                            'grab',
                            'grabbing',
                            'all-scroll',
                            'col-resize',
                            'row-resize',
                            'n-resize',
                            'e-resize',
                            's-resize',
                            'w-resize',
                            'ne-resize',
                            'nw-resize',
                            'se-resize',
                            'sw-resize',
                            'ew-resize',
                            'ns-resize',
                            'nesw-resize',
                            'nwse-resize',
                            'zoom-in',
                            'zoom-out',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Field Sizing
                 *
                 * @see https://tailwindcss.com/docs/field-sizing
                 */
                'field-sizing' => [['field-sizing' => ['fixed', 'content']]],
                /*
                 * Pointer Events
                 *
                 * @see https://tailwindcss.com/docs/pointer-events
                 */
                'pointer-events' => [['pointer-events' => ['auto', 'none']]],
                /*
                 * Resize
                 *
                 * @see https://tailwindcss.com/docs/resize
                 */
                'resize' => [['resize' => ['none', '', 'y', 'x']]],
                /*
                 * Scroll Behavior
                 *
                 * @see https://tailwindcss.com/docs/scroll-behavior
                 */
                'scroll-behavior' => [['scroll' => ['auto', 'smooth']]],
                /*
                 * Scroll Margin
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-m' => [['scroll-m' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Margin X
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-mx' => [['scroll-mx' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Margin Y
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-my' => [['scroll-my' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Margin Start
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-ms' => [['scroll-ms' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Margin End
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-me' => [['scroll-me' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Margin Top
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-mt' => [['scroll-mt' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Margin Right
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-mr' => [['scroll-mr' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Margin Bottom
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-mb' => [['scroll-mb' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Margin Left
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-ml' => [['scroll-ml' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Padding
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-p' => [['scroll-p' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Padding X
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-px' => [['scroll-px' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Padding Y
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-py' => [['scroll-py' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Padding Start
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-ps' => [['scroll-ps' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Padding End
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pe' => [['scroll-pe' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Padding Top
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pt' => [['scroll-pt' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Padding Right
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pr' => [['scroll-pr' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Padding Bottom
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pb' => [['scroll-pb' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Padding Left
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pl' => [['scroll-pl' => self::scaleUnambiguousSpacing($themeSpacing)]],
                /*
                 * Scroll Snap Align
                 *
                 * @see https://tailwindcss.com/docs/scroll-snap-align
                 */
                'snap-align' => [['snap' => ['start', 'end', 'center', 'align-none']]],
                /*
                 * Scroll Snap Stop
                 *
                 * @see https://tailwindcss.com/docs/scroll-snap-stop
                 */
                'snap-stop' => [['snap' => ['normal', 'always']]],
                /*
                 * Scroll Snap Type
                 *
                 * @see https://tailwindcss.com/docs/scroll-snap-type
                 */
                'snap-type' => [['snap' => ['none', 'x', 'y', 'both']]],
                /*
                 * Scroll Snap Type Strictness
                 *
                 * @see https://tailwindcss.com/docs/scroll-snap-type
                 */
                'snap-strictness' => [['snap' => ['mandatory', 'proximity']]],
                /*
                 * Touch Action
                 *
                 * @see https://tailwindcss.com/docs/touch-action
                 */
                'touch' => [['touch' => ['auto', 'none', 'manipulation']]],
                /*
                 * Touch Action X
                 *
                 * @see https://tailwindcss.com/docs/touch-action
                 */
                'touch-x' => [['touch-pan' => ['x', 'left', 'right']]],
                /*
                 * Touch Action Y
                 *
                 * @see https://tailwindcss.com/docs/touch-action
                 */
                'touch-y' => [['touch-pan' => ['y', 'up', 'down']]],
                /*
                 * Touch Action Pinch Zoom
                 *
                 * @see https://tailwindcss.com/docs/touch-action
                 */
                'touch-pz' => ['touch-pinch-zoom'],
                /*
                 * User Select
                 *
                 * @see https://tailwindcss.com/docs/user-select
                 */
                'select' => [['select' => ['none', 'text', 'all', 'auto']]],
                /*
                 * Will Change
                 *
                 * @see https://tailwindcss.com/docs/will-change
                 */
                'will-change' => [
                    [
                        'will-change' => [
                            'auto',
                            'scroll',
                            'contents',
                            'transform',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],

                // -----------
                // --- SVG ---
                // -----------

                /*
                 * Fill
                 *
                 * @see https://tailwindcss.com/docs/fill
                 */
                'fill' => [['fill' => ['none', ...self::scaleColor($themeColor)]]],
                /*
                 * Stroke Width
                 *
                 * @see https://tailwindcss.com/docs/stroke-width
                 */
                'stroke-w' => [
                    [
                        'stroke' => [
                            NumberValidator::validate(...),
                            ArbitraryVariableLengthValidator::validate(...),
                            ArbitraryValueLengthValidator::validate(...),
                            ArbitraryValueNumberValidator::validate(...),
                        ],
                    ],
                ],
                /*
                 * Stroke
                 *
                 * @see https://tailwindcss.com/docs/stroke
                 */
                'stroke' => [['stroke' => ['none', ...self::scaleColor($themeColor)]]],

                // ---------------------
                // --- Accessibility ---
                // ---------------------

                /*
                 * Forced Color Adjust
                 *
                 * @see https://tailwindcss.com/docs/forced-color-adjust
                 */
                'forced-color-adjust' => [['forced-color-adjust' => ['auto', 'none']]],
            ],
            'conflictingClassGroups' => [
                'overflow' => ['overflow-x', 'overflow-y'],
                'overscroll' => ['overscroll-x', 'overscroll-y'],
                'inset' => ['inset-x', 'inset-y', 'start', 'end', 'top', 'right', 'bottom', 'left'],
                'inset-x' => ['right', 'left'],
                'inset-y' => ['top', 'bottom'],
                'flex' => ['basis', 'grow', 'shrink'],
                'gap' => ['gap-x', 'gap-y'],
                'p' => ['px', 'py', 'ps', 'pe', 'pt', 'pr', 'pb', 'pl'],
                'px' => ['pr', 'pl'],
                'py' => ['pt', 'pb'],
                'm' => ['mx', 'my', 'ms', 'me', 'mt', 'mr', 'mb', 'ml'],
                'mx' => ['mr', 'ml'],
                'my' => ['mt', 'mb'],
                'size' => ['w', 'h'],
                'font-size' => ['leading'],
                'fvn-normal' => [
                    'fvn-ordinal',
                    'fvn-slashed-zero',
                    'fvn-figure',
                    'fvn-spacing',
                    'fvn-fraction',
                ],
                'fvn-ordinal' => ['fvn-normal'],
                'fvn-slashed-zero' => ['fvn-normal'],
                'fvn-figure' => ['fvn-normal'],
                'fvn-spacing' => ['fvn-normal'],
                'fvn-fraction' => ['fvn-normal'],
                'line-clamp' => ['display', 'overflow'],
                'rounded' => [
                    'rounded-s',
                    'rounded-e',
                    'rounded-t',
                    'rounded-r',
                    'rounded-b',
                    'rounded-l',
                    'rounded-ss',
                    'rounded-se',
                    'rounded-ee',
                    'rounded-es',
                    'rounded-tl',
                    'rounded-tr',
                    'rounded-br',
                    'rounded-bl',
                ],
                'rounded-s' => ['rounded-ss', 'rounded-es'],
                'rounded-e' => ['rounded-se', 'rounded-ee'],
                'rounded-t' => ['rounded-tl', 'rounded-tr'],
                'rounded-r' => ['rounded-tr', 'rounded-br'],
                'rounded-b' => ['rounded-br', 'rounded-bl'],
                'rounded-l' => ['rounded-tl', 'rounded-bl'],
                'border-spacing' => ['border-spacing-x', 'border-spacing-y'],
                'border-w' => [
                    'border-w-x',
                    'border-w-y',
                    'border-w-s',
                    'border-w-e',
                    'border-w-t',
                    'border-w-r',
                    'border-w-b',
                    'border-w-l',
                ],
                'border-w-x' => ['border-w-r', 'border-w-l'],
                'border-w-y' => ['border-w-t', 'border-w-b'],
                'border-color' => [
                    'border-color-x',
                    'border-color-y',
                    'border-color-s',
                    'border-color-e',
                    'border-color-t',
                    'border-color-r',
                    'border-color-b',
                    'border-color-l',
                ],
                'border-color-x' => ['border-color-r', 'border-color-l'],
                'border-color-y' => ['border-color-t', 'border-color-b'],
                'translate' => ['translate-x', 'translate-y', 'translate-none'],
                'translate-none' => ['translate', 'translate-x', 'translate-y', 'translate-z'],
                'scroll-m' => [
                    'scroll-mx',
                    'scroll-my',
                    'scroll-ms',
                    'scroll-me',
                    'scroll-mt',
                    'scroll-mr',
                    'scroll-mb',
                    'scroll-ml',
                ],
                'scroll-mx' => ['scroll-mr', 'scroll-ml'],
                'scroll-my' => ['scroll-mt', 'scroll-mb'],
                'scroll-p' => [
                    'scroll-px',
                    'scroll-py',
                    'scroll-ps',
                    'scroll-pe',
                    'scroll-pt',
                    'scroll-pr',
                    'scroll-pb',
                    'scroll-pl',
                ],
                'scroll-px' => ['scroll-pr', 'scroll-pl'],
                'scroll-py' => ['scroll-pt', 'scroll-pb'],
                'touch' => ['touch-x', 'touch-y', 'touch-pz'],
                'touch-x' => ['touch'],
                'touch-y' => ['touch'],
                'touch-pz' => ['touch'],
            ],
            'conflictingClassGroupModifiers' => [
                'font-size' => ['leading'],
            ],
            'orderSensitiveModifiers' => [
                '*',
                '**',
                'after',
                'backdrop',
                'before',
                'details-content',
                'file',
                'first-letter',
                'first-line',
                'marker',
                'placeholder',
                'selection',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $additionalConfig
     */
    public static function setAdditionalConfig(array $additionalConfig): void
    {
        self::$additionalConfig = $additionalConfig;
    }

    public static function fromTheme(string $key): ThemeGetter
    {
        return new ThemeGetter($key);
    }

    /**
     * @return array<int, string|callable>
     */
    private static function scaleBreak(): array
    {
        return [
            'auto', 'avoid', 'all', 'avoid-page', 'page', 'left', 'right', 'column',
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function scalePosition(): array
    {
        return [
            'center',
            'top',
            'bottom',
            'left',
            'right',
            'top-left',
            // Deprecated since Tailwind CSS v4.1.0, see https://github.com/tailwindlabs/tailwindcss/pull/17378
            'left-top',
            'top-right',
            // Deprecated since Tailwind CSS v4.1.0, see https://github.com/tailwindlabs/tailwindcss/pull/17378
            'right-top',
            'bottom-right',
            // Deprecated since Tailwind CSS v4.1.0, see https://github.com/tailwindlabs/tailwindcss/pull/17378
            'right-bottom',
            'bottom-left',
            // Deprecated since Tailwind CSS v4.1.0, see https://github.com/tailwindlabs/tailwindcss/pull/17378
            'left-bottom',
        ];
    }

    /**
     * @return list<string|callable>
     */
    private static function scalePositionWithArbitrary(): array
    {
        return [
            ...self::scalePosition(),
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string>
     */
    private static function scaleOverflow(): array
    {
        return [
            'auto', 'hidden', 'clip', 'visible', 'scroll',
        ];
    }

    /**
     * @return list<string|callable>
     */
    private static function scaleOverscroll(): array
    {
        return [
            'auto', 'contain', 'none',
        ];
    }

    /**
     * @return list<string|callable|ThemeGetter>
     */
    private static function scaleUnambiguousSpacing(ThemeGetter $themeSpacing): array
    {
        return [
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
            $themeSpacing,
        ];
    }

    /**
     * @return list<string|callable|ThemeGetter>
     */
    private static function scaleInset(ThemeGetter $themeSpacing): array
    {
        return [
            FractionValidator::validate(...),
            'full',
            'auto',
            ...self::scaleUnambiguousSpacing($themeSpacing),
        ];
    }

    /**
     * @return list<string|callable>
     */
    private static function scaleGridTemplateColsRows(): array
    {
        return [
            IntegerValidator::validate(...),
            'none',
            'subgrid',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable|array<string, list<string|callable>>>
     */
    private static function scaleGridColRowStartAndEnd(): array
    {
        return [
            'auto',
            [
                'span' => [
                    'full',
                    IntegerValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
            ],
            IntegerValidator::validate(...),
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable>
     */
    private static function scaleGridColRowStartOrEnd(): array
    {
        return [
            IntegerValidator::validate(...),
            'auto',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable>
     */
    private static function scaleGridAutoColsRows(): array
    {
        return [
            'auto',
            'min',
            'max',
            'fr',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string>
     */
    private static function scaleAlignPrimaryAxis(): array
    {
        return [
            'start',
            'end',
            'center',
            'between',
            'around',
            'evenly',
            'stretch',
            'baseline',
            'center-safe',
            'end-safe',
        ];
    }

    /**
     * @return list<string>
     */
    private static function scaleAlignSecondaryAxis(): array
    {
        return [
            'start',
            'end',
            'center',
            'stretch',
            'center-safe',
            'end-safe',
        ];
    }

    /**
     * @return list<string|callable|ThemeGetter>
     */
    private static function scaleMargin(ThemeGetter $themeSpacing): array
    {
        return [
            'auto',
            ...self::scaleUnambiguousSpacing($themeSpacing),
        ];
    }

    /**
     * @return list<string|callable|ThemeGetter>
     */
    private static function scaleSizing(ThemeGetter $themeSpacing): array
    {
        return [
            FractionValidator::validate(...),
            'auto',
            'full',
            'dvw',
            'dvh',
            'lvw',
            'lvh',
            'svw',
            'svh',
            'min',
            'max',
            'fit',
            ...self::scaleUnambiguousSpacing($themeSpacing),
        ];
    }

    /**
     * @return list<string|callable|ThemeGetter>
     */
    private static function scaleColor(ThemeGetter $themeColor): array
    {
        return [
            $themeColor,
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable|array<string, list<callable>>>
     */
    private static function scaleBgPosition(): array
    {
        return [
            ...self::scalePosition(),
            ArbitraryVariablePositionValidator::validate(...),
            ArbitraryValuePositionValidator::validate(...),
            [
                'position' => [
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
            ],
        ];
    }

    /**
     * @return list<string|array<string, list<string>>>
     */
    private static function scaleBgRepeat(): array
    {
        return [
            'no-repeat',
            [
                'repeat' => [
                    '',
                    'x',
                    'y',
                    'space',
                    'round',
                ],
            ],
        ];
    }

    /**
     * @return list<string|callable|array<string, list<callable>>>
     */
    private static function scaleBgSize(): array
    {
        return [
            'auto',
            'cover',
            'contain',
            ArbitraryVariableSizeValidator::validate(...),
            ArbitraryValueSizeValidator::validate(...),
            [
                'size' => [
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
            ],
        ];
    }

    /**
     * @return list<callable>
     */
    private static function scaleGradientStopPosition(): array
    {
        return [
            PercentValidator::validate(...),
            ArbitraryVariableLengthValidator::validate(...),
            ArbitraryValueLengthValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable|ThemeGetter>
     */
    private static function scaleRadius(ThemeGetter $themeRadius): array
    {
        return [
            // Deprecated since Tailwind CSS v4.0.0
            '',
            'none',
            'full',
            $themeRadius,
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable>
     */
    private static function scaleBorderWidth(): array
    {
        return [
            '',
            NumberValidator::validate(...),
            ArbitraryVariableLengthValidator::validate(...),
            ArbitraryValueLengthValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable>
     */
    private static function scaleLineStyle(): array
    {
        return ['solid', 'dashed', 'dotted', 'double'];
    }

    /**
     * @return list<string|callable>
     */
    private static function scaleOrigin(): array
    {
        return [
            'center',
            'top',
            'top-right',
            'right',
            'bottom-right',
            'bottom',
            'bottom-left',
            'left',
            'top-left',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string>
     */
    private static function scaleBlendMode(): array
    {
        return [
            'normal',
            'multiply',
            'screen',
            'overlay',
            'darken',
            'lighten',
            'color-dodge',
            'color-burn',
            'hard-light',
            'soft-light',
            'difference',
            'exclusion',
            'hue',
            'saturation',
            'color',
            'luminosity',
        ];
    }

    /**
     * @return list<callable>
     */
    private static function scaleMaskImagePosition(): array
    {
        return [
            NumberValidator::validate(...),
            PercentValidator::validate(...),
            ArbitraryVariableValidator::validate(...),
            ArbitraryValuePositionValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable|ThemeGetter>
     */
    private static function scaleBlur(ThemeGetter $themeBlur): array
    {
        return [
            // Deprecated since Tailwind CSS v4.0.0
            '',
            'none',
            $themeBlur,
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable>
     */
    private static function scaleRotate(): array
    {
        return [
            'none',
            NumberValidator::validate(...),
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<string|callable>
     */
    private static function scaleScale(): array
    {
        return [
            'none',
            NumberValidator::validate(...),
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return list<callable|ThemeGetter>
     */
    private static function scaleSkew(ThemeGetter $themeSpacing): array
    {
        return [
            NumberValidator::validate(...),
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
            $themeSpacing,
        ];
    }

    /**
     * @return list<string|callable|ThemeGetter>
     */
    private static function scaleTranslate(ThemeGetter $themeSpacing): array
    {
        return [
            FractionValidator::validate(...),
            'full',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
            $themeSpacing,
        ];
    }

    private static function mergePropertyRecursively(array $baseConfig, string $mergeKey, array|bool|float|int|string|null $mergeValue): array|bool|float|int|string|null
    {
        if (!\array_key_exists($mergeKey, $baseConfig)) {
            return $mergeValue;
        }

        if (\is_string($mergeValue)) {
            return $mergeValue;
        }

        if (is_numeric($mergeValue)) {
            return $mergeValue;
        }

        if (\is_bool($mergeValue)) {
            return $mergeValue;
        }

        if (null === $mergeValue) {
            return null;
        }

        if (array_is_list($mergeValue) && \is_array($baseConfig[$mergeKey]) && array_is_list($baseConfig[$mergeKey])) {
            return [...$baseConfig[$mergeKey], ...$mergeValue];
        }

        if (!array_is_list($mergeValue)) {
            if (null === $baseConfig[$mergeKey]) {
                return $mergeValue;
            }

            foreach ($mergeValue as $key => $value) {
                $baseConfig[$mergeKey][$key] = self::mergePropertyRecursively($baseConfig[$mergeKey], $key, $value);
            }
        }

        return $baseConfig[$mergeKey];
    }
}
