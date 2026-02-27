<?php

use Kirby\Cms\App;

@include_once __DIR__ . '/vendor/autoload.php';

App::plugin('tobimori/tailwind-merge', [
  'options' => [
    'config' => [],
    'cache' => true
  ]
]);
