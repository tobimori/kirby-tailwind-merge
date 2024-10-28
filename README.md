![Kirby Tailwind Merge Banner](.github/banner.png)

# Kirby Tailwind Merge

Intelligently merge Tailwind classes without style conflicts in your Kirby templates.

![Example for Tailwind Merge](.github/example.png)

This plugin relies on [tailwind-merge-php by Sandro Gehri](https://github.com/gehrisandro/tailwind-merge-php) for merging classes and only adapts it to work in the "Kirby ecosystem". Any issues related to merging classes should probably reported over there.

## Installation

```
composer require tobimori/kirby-tailwind-merge
```

#### Manual installation

Download and copy this repository to `/site/plugins/kirby-tailwind-merge`, or apply this repository as Git submodule.

## Usage

This plugin provides two helper functions to use in your blueprints. Whether functions should be registered can be controlled in your `config.php`, see [Options](#options).

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

### `mod($modifier, $classes)`

`mod()` applies the specified modifier/variant to each class supplied in the `$class` string. It also applies Tailwind Merge behaviour and outputs the contents of class attribute. This is useful when you have a bunch of classes and want them all to activate at the same modifier.

#### Example

```php
<div class="flex mb-4 <?= mod('lg', 'mb-2 flex-col') ?>">[...]</div>

// output
<div class="flex mb-4 lg:mb-2 lg:flex-col">[...]</div>
```

#### "But Tailwind won't parse my classes then!"

I hear you, but thankfully Tailwind allows us to customize the parser to our needs. This is not a 100% perfect technique due to being reliant on regexing' the classes, but it works for most cases.

With a custom transformer function to scan for the `mod()` function, your `tailwind.config.js` could look like this:

```js
module.exports = {
  content: {
    files: ["./site/**/*.php", "./src/index.js"],
    transform: (code) => {
      const variantGroupsRegex = /mod\(.([^,"']+)[^\[]+["'](.+)["']\)/g
      const variantGroupMatches = [...code.matchAll(variantGroupsRegex)]

      variantGroupMatches.forEach(([matchStr, variants, classes]) => {
        const parsedClasses = classes
          .split(" ")
          .map((cls) => `${variants}:${cls}`)
          .join(" ")

        code = code.replaceAll(matchStr, parsedClasses)
      })

      return code
    }
  },
  theme: {
    extend: {}
  },
  plugins: []
}
```

For simplicity in parsing the function with Tailwind, the `mod()` function doesn't support arrays. With this approach, you're also not aple to e.g. use a variable inside the function, but only direct strings.

If you still want to use variables, that e.g. come from the CMS directly, you can add the generated classes to your [`safelist`](https://tailwindcss.com/docs/content-configuration#safelisting-classes) and they'll be generated to matter what.

## Options

| Option   | Default | Description                                           |
| -------- | ------- | ----------------------------------------------------- |
| `prefix` | `''`    | Set a prefix for your tailwind classes                |
| `cache`  | `false` | Enable caching for tailwind merge using a Kirby Cache |

Options allow you to fine tune the behaviour of the plugin. You can set them in your `config.php` file:

```php
return [
    'tobimori.tailwind-merge' => [
        'prefix' => 'tw-',
        'cache' => true
    ],
];
```

## Support

> This plugin is provided free of charge & published under the permissive MIT License. If you use it in a commercial project, please consider to [sponsor me on GitHub](https://github.com/sponsors/tobimori) to support further development and continued maintenance of my plugins.

## License

[MIT License](./LICENSE)
Copyright © 2023 Tobias Möritz
