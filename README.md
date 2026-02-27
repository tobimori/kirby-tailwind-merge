![Kirby Tailwind Merge Banner](.github/banner.png)

# Kirby Tailwind Merge

Intelligently merge Tailwind CSS v4 classes without style conflicts in your Kirby templates.

![Example for Tailwind Merge](.github/example.png)

This plugin relies on [tailwind-merge-php by Tales from a Dev](https://github.com/tales-from-a-dev/tailwind-merge-php) for merging classes and only adapts it to work in the "Kirby ecosystem". Any issues related to merging classes should probably reported over there.

## Installation

```
composer require tobimori/kirby-tailwind-merge
```

## Usage

This plugin provides helper functions to use in your templates.

### `attr()`

This helper function works similar to the Kirby built-in `attr()` function and overwrites it to support Tailwind Merge behaviour for the `class` attribute.

You'll need to disable the built-in `attr()` helper at the top-most location in your `index.php` file - before Kirby is loaded.

```php
define("KIRBY_HELPER_ATTR", false);
```

#### Example

```php
// site/snippets/component.php
<div <?= attr(['class' => ['h-full w-full bg-neutral-100', $class], 'data-attr' => 'hello world!']) ?>>[...]</div>

// site/templates/default.php
<?php snippet('component', ['class' => 'w-1/2']) ?>

// output
<div class="w-1/2 h-full bg-neutral-100" data-attr="hello world!">[...]</div>
```

### `merge()`

`merge()` applies Tailwind Merge behaviour and outputs a class attribute.

#### Example

```php
// site/snippets/component.php
<div <?= merge('h-full w-full bg-neutral-100', $class) ?>>[...]</div>

// site/templates/default.php
<?php snippet('component', ['class' => 'w-1/2']) ?>

// output
<div class="w-1/2 h-full bg-neutral-100">[...]</div>
```

### `cls()`

`cls()` applies Tailwind Merge behaviour and outputs the contents of class attribute. This can be used to work better with the conditional merge syntax this plugin provides, and also for nesting.

#### Example

```php
// site/snippets/blocks/simple-text.php
<div class="<?= cls([
        'bg-neutral-white',
        'py-32' => true,
        cls([
            'px-16' => $block->type() !== 'simple-text',
            'px-8' => $block->type() === 'centered-text'
        ]) => $page->intendedTemplate() == 'home'
    ]); ?>">[...]</div>

// site/templates/home.php
// output
<div class="px-16 py-32 bg-neutral-white">[...]</div>

// site/templates/article.php
// output
<div class="py-32 bg-neutral-white">[...]</div>
```

### Conditional merging

This conditional merge syntax using arrays can be used with the `merge()` and `attr()` functions as well.

```php
<div <?= merge([
        'bg-neutral-white', // always applied if no condition is present
        'py-32' => true, // always applied, because condition is true
        cls([ // this works like an "AND", ANY entries in cls function will only be applied if the condition is true, this results in...
            'px-16' => $block->type() !== 'simple-text', // applied when block type is not 'simple-text', but intendedTemplate is 'home'
            'px-8' => $block->type() === 'centered-text' // applied when block type is 'centered-text' and intendedTemplate is 'home', also replaces 'px-16' from above
        ]) => $page->intendedTemplate() == 'home' // "parent" AND condition
    ]) ?>>[...]</div>
```

## Options

| Option   | Default | Description                                                                   |
| -------- | ------- | ----------------------------------------------------------------------------- |
| `config` | `[]`    | Additional [tailwind-merge configuration][tw-merge-config] (array or closure) |
| `cache`  | `true`  | Disable caching using Kirbys Cache                                            |

Options can be set in your `config.php` file:

```php
return [
    'tobimori.tailwind-merge' => [
        'config' => [
            'prefix' => 'tw-',
        ],
        'cache' => true
    ],
];
```

The `config` option also accepts a closure for dynamic configuration. It supports all keys from the [underlying library][tw-merge-config] — the most useful ones being `prefix`, `theme`, `classGroups`, and `conflictingClassGroups`.

### Custom theme values

If you're using Tailwind CSS v4 without custom `@theme` values, the plugin works out of the box: standard class names, numeric values, and arbitrary values are all handled automatically.

However, if you've defined custom `@theme` variables in your CSS, you need to register those names so tailwind-merge can recognize them as conflicting. For example, given a config like this:

```css
@theme {
    --font-heading: "TT Interphases Pro Condensed", sans-serif;
    --font-sans: "TT Interphases Pro", sans-serif;
    --font-handwriting: "Caveat", cursive;

    --text-h1: clamp(2.25rem, /* ... */, 4rem);
    --text-h2: clamp(1.75rem, /* ... */, 3rem);
    --text-h3: clamp(1.5rem, /* ... */, 2rem);
    --text-h4: clamp(1.375rem, /* ... */, 1.75rem);

    --font-weight-normal: 500;
    --font-weight-semibold: 600;
    --font-weight-bold: 700;
}
```

You'd extend the `theme` config with the custom value names (the part after the last `-` in the CSS variable):

```php
return [
    'tobimori.tailwind-merge' => [
        'config' => [
            'theme' => [
                'text' => ['h1', 'h2', 'h3', 'h4'],
                'font' => ['heading', 'sans', 'handwriting'],
                'font-weight' => ['normal', 'semibold', 'bold'],
            ],
        ],
    ],
];
```

These values are merged with the built-in defaults, so you don't need to re-declare standard names. Without this config, `merge('text-h1', 'text-h2')` would keep both classes — with it, the plugin correctly resolves to `text-h2`.

[tw-merge-config]: https://github.com/tales-from-a-dev/tailwind-merge-php/blob/main/docs/index.md#configuration

## Support

> This plugin is provided free of charge & published under the permissive MIT License. If you use it in a commercial project, please consider to [sponsor me on GitHub](https://github.com/sponsors/tobimori) to support further development and continued maintenance of my plugins.

## License

[MIT License](./LICENSE)
Copyright © 2023-2026 Tobias Möritz
