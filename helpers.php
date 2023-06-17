<?php

use tobimori\TwMerge;

if (option('tobimori.tailwind-merge.helpers.attr')) {
  /**
   * Generates a list of HTML attributes, and intelligently merges classes with Tailwind merge.
   *
   * @param array|null $attr A list of attributes as key/value array
   * @param string|null $before An optional string that will be prepended if the result is not empty
   * @param string|null $after An optional string that will be appended if the result is not empty
   */
  function attr(
    array|null $attr = null,
    string|null $before = null,
    string|null $after = null
  ): string|null {
    return TwMerge::attr($attr, null, $before, $after);
  }
}

if (option('tobimori.tailwind-merge.helpers.merge')) {
  /**
   * Outputs the class html attribute and intelligently merges classes with Tailwind merge.
   *
   * @param string|array $classes A list of classes as string or array
   */
  function merge(string|array $classes): string
  {
    return TwMerge::merge($classes);
  }
}
