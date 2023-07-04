<?php

use tobimori\TwMerge;

if (!function_exists('attr')) {
  /**
   * Generates a list of HTML attributes, and intelligently merges classes with Tailwind Merge.
   */
  function attr(
    array|null $attr = null,
    string|null $before = null,
    string|null $after = null
  ): string|null {
    return TwMerge::attr($attr, null, $before, $after);
  }
}

if (!function_exists('merge')) {
  /**
   * Outputs the class html attribute and intelligently merges classes with Tailwind Merge.
   */
  function merge(...$classes): string
  {
    return TwMerge::merge($classes);
  }
}

if (!function_exists('cls')) {
  /**
   * Outputs the contents of the class html attribute and intelligently merges classes with Tailwind Merge.
   */
  function cls(...$classes): string
  {
    return TwMerge::cls($classes);
  }
}

if (!function_exists('mod')) {
  /**
   * Modifies all classes with the given modifier and intelligently merges classes with Tailwind Merge.
   */
  function mod(string $modifier, string $classes): string
  {
    return TwMerge::modify($modifier, $classes);
  }
}
