<?php

namespace tobimori;

use DateInterval;
use Kirby\Cache\Cache;
use Psr\SimpleCache\CacheInterface;

class KirbyCacheAdapter implements CacheInterface {
  protected Cache $cache;

  public function __construct(string $name)
  {
    $this->cache = kirby()->cache($name);
  }

  public function get(string $key, mixed $default = null): mixed {
    return $this->cache->get($key) ?? $default;
  }

  public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool {
    if($ttl instanceof DateInterval) {
      $ttl = ceil($ttl->s / 60);
    } elseif($ttl === null) {
      $ttl = 0;
    } elseif($ttl < 0) {
      $ttl = 0;
    } elseif(is_int($ttl)) {
      $ttl = ceil($ttl / 60);
    }

    return $this->cache->set($key, $value, $ttl);
  }

  public function delete(string $key): bool {
    return $this->cache->remove($key);
  }

  public function clear(): bool {
    return $this->cache->flush();
  }

  public function getMultiple(iterable $keys, mixed $default = null): iterable {
    $values = [];
    foreach($keys as $key) {
      $values[$key] = $this->cache->get($key) ?? $default;
    }
    return $values;
  }

  public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool {
    foreach($values as $key => $value) {
      $this->set($key, $value, $ttl);
    }
    return true;	
  }

  public function deleteMultiple(iterable $keys): bool {
    foreach($keys as $key) {
      $this->delete($key);
    }
    return true;
  }

  public function has(string $key): bool {
    return $this->cache->exists($key);
  }
}