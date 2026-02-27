<?php

namespace tobimori;

use Kirby\Toolkit\Html;
use TalesFromADev\TailwindMerge\TailwindMerge;

class TwMerge
{
  private static TailwindMerge $_instance;

  public static function instance()
  {
    if (!isset(self::$_instance)) {
      $config = option('tobimori.tailwind-merge.config', []);

      if (is_callable($config)) {
        $config = $config();
      }

      $cache = option('tobimori.tailwind-merge.cache', true)
        ? new KirbyCacheAdapter('tobimori.tailwind-merge')
        : null;

      self::$_instance = new TailwindMerge(
        additionalConfiguration: $config,
        cache: $cache,
      );
    }

    return self::$_instance;
  }

  public static function buildClassAttr(string|array $classes): string
  {
    if (is_string($classes)) {
      return $classes;
    }

    $classList = [];

    foreach ($classes as $class => $condition) {
      if (is_numeric($class)) {
        if (is_array($condition)) {
          $classList[] = self::buildClassAttr($condition);
        } else {
          $classList[] = $condition;
        }
      } elseif ($condition) {
        if (is_array($class)) {
          $classList[] = self::buildClassAttr($class);
        } else {
          $classList[] = $class;
        }
      }
    }

    return implode(' ', $classList);
  }

  public static function cls(string|array $value): string
  {
    return self::instance()->merge(self::buildClassAttr($value)) ?? ' ';
  }

  public static function attr(
    string|array $name,
    $value = null,
    string|null $before = null,
    string|null $after = null
  ): string|null {
    if ($name === 'class') {
      $value = self::cls($value);
    }

    if (is_array($name) && isset($name['class'])) {
      $name['class'] = self::cls($name['class']);
    }

    return Html::attr($name, $value, $before, $after);
  }

  public static function merge(...$classes): string
  {
    return self::attr('class', $classes) ?? ' ';
  }
}
