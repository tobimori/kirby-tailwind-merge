<?php

namespace tobimori;

use Kirby\Toolkit\Html;
use TailwindMerge\TailwindMerge;

class TwMerge
{
  public static function instance()
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
      $value =  self::instance()->merge($value);
    }

    if (is_array($name) && isset($name['class'])) {
      $name['class'] = self::instance()->merge($name['class']);
    }

    return Html::attr($name, $value, $before, $after);
  }

  public static function merge(string|array $classes, ...$args): string
  {
    return self::attr('class', [$classes, ...$args]);
  }
}
