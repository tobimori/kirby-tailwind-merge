<?php

use tobimori\TwMerge;

if (option('tobimori.tailwind-merge.helpers.attr', true)) {
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

if (option('tobimori.tailwind-merge.helpers.merge', true)) {
  /**
   * Outputs the class html attribute and intelligently merges classes with Tailwind Merge.
   */
  function merge(...$classes): string
  {
    return TwMerge::merge($classes);
  }
}

if (option('tobimori.tailwind-merge.helpers.cls', true)) {
  /**
   * Outputs the contents of the class html attribute and intelligently merges classes with Tailwind Merge.
   */
  function cls(...$classes): string
  {
    return TwMerge::cls($classes);
  }
}
