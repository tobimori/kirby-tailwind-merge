<?php

namespace tobimori;

use Kirby\Toolkit\Html;
use TailwindMerge\TailwindMerge;

class TwMerge
{
  public function __construct()
  {
    return TailwindMerge::factory()->withConfiguration([
      'prefix' => option('tobimori.tailwind-merge.prefix', '')
    ])->make();
  }

  public static function attr(
    string|array $name,
    $value = null,
    string|null $before = null,
    string|null $after = null
  ): string|null {
    if ($name === 'class') {
      $value = (new self())->attr($value);
    }

    if (is_array($name) && isset($name['class'])) {
      $name['class'] = (new self())->attr($name['class']);
    }

    return Html::attr($name, $value, $before, $after);
  }

  public static function merge(string|array $classes): string
  {
    return self::attr('class', $classes);
  }
}
