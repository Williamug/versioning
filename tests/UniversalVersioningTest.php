<?php

use Williamug\Versioning\UniversalVersioning;

beforeEach(function () {
  UniversalVersioning::clearCache();
  UniversalVersioning::setRepositoryPath(base_path());
  UniversalVersioning::setFallbackVersion('dev');
  UniversalVersioning::setIncludePrefix(true);
  UniversalVersioning::setCacheAdapter(null);
});

it('can get version tag', function () {
  $version = UniversalVersioning::tag();

  expect($version)->toBeString();
});

it('can get full version', function () {
  $version = UniversalVersioning::full();

  expect($version)->toBeString();
});

it('can get commit hash', function () {
  $version = UniversalVersioning::commit();

  expect($version)->toBeString();
});

it('can get tag with commit', function () {
  $version = UniversalVersioning::tagWithCommit();

  expect($version)->toBeString();
});

it('returns fallback version when git is not available', function () {
  UniversalVersioning::setRepositoryPath('/nonexistent/path');

  $version = UniversalVersioning::tag();

  expect($version)->toBe('dev');
});

it('uses custom fallback version', function () {
  UniversalVersioning::setRepositoryPath('/nonexistent/path');
  UniversalVersioning::setFallbackVersion('v0.0.0');

  $version = UniversalVersioning::tag();

  expect($version)->toBe('v0.0.0');
});

it('can configure repository path', function () {
  UniversalVersioning::setRepositoryPath(base_path());

  $version = UniversalVersioning::tag();

  expect($version)->toBeString();
});

it('removes prefix when configured', function () {
  UniversalVersioning::setIncludePrefix(false);
  UniversalVersioning::setRepositoryPath(base_path());

  $version = UniversalVersioning::tag();

  expect($version)->toMatch('/^(\d|dev)/');
});

it('works with psr-16 cache adapter', function () {
  $cache = new class {
    private $data = [];

    public function get($key, $default = null)
    {
      return $this->data[$key] ?? $default;
    }

    public function set($key, $value, $ttl = null)
    {
      $this->data[$key] = $value;
      return true;
    }

    public function delete($key)
    {
      unset($this->data[$key]);
      return true;
    }
  };

  UniversalVersioning::setCacheAdapter($cache);
  UniversalVersioning::setRepositoryPath(base_path());

  $version1 = UniversalVersioning::tag();
  $version2 = UniversalVersioning::tag();

  expect($version1)->toBe($version2);
});

it('works with psr-6 cache adapter', function () {
  $cache = new class {
    private $items = [];

    public function getItem($key)
    {
      return $this->items[$key] ?? new class($key) {
        private $key;
        private $value = null;
        private $hit = false;

        public function __construct($key)
        {
          $this->key = $key;
        }

        public function get()
        {
          return $this->value;
        }

        public function set($value)
        {
          $this->value = $value;
          $this->hit = true;
          return $this;
        }

        public function isHit()
        {
          return $this->hit;
        }

        public function expiresAfter($time)
        {
          return $this;
        }

        public function getKey()
        {
          return $this->key;
        }
      };
    }

    public function save($item)
    {
      $this->items[$item->getKey()] = $item;
      return true;
    }

    public function deleteItem($key)
    {
      unset($this->items[$key]);
      return true;
    }
  };

  UniversalVersioning::setCacheAdapter($cache);
  UniversalVersioning::setRepositoryPath(base_path());

  $version = UniversalVersioning::tag();

  expect($version)->toBeString();
});

it('works with laravel-style cache', function () {
  $cache = new class {
    private $data = [];

    public function has($key)
    {
      return isset($this->data[$key]);
    }

    public function get($key)
    {
      return $this->data[$key] ?? null;
    }

    public function put($key, $value, $ttl)
    {
      $this->data[$key] = $value;
    }

    public function forget($key)
    {
      unset($this->data[$key]);
    }
  };

  UniversalVersioning::setCacheAdapter($cache);
  UniversalVersioning::setRepositoryPath(base_path());

  $version1 = UniversalVersioning::tag();
  $version2 = UniversalVersioning::tag();

  expect($version1)->toBe($version2);
});

it('handles cache failures gracefully', function () {
  $cache = new class {
    public function get($key)
    {
      throw new \Exception('Cache failure');
    }

    public function set($key, $value, $ttl)
    {
      throw new \Exception('Cache failure');
    }
  };

  UniversalVersioning::setCacheAdapter($cache);
  UniversalVersioning::setRepositoryPath(base_path());

  $version = UniversalVersioning::tag();

  expect($version)->toBeString();
});

it('can clear cache with psr-16 adapter', function () {
  $cache = new class {
    public $deleted = [];

    public function get($key, $default = null)
    {
      return null;
    }

    public function set($key, $value, $ttl = null)
    {
      return true;
    }

    public function delete($key)
    {
      $this->deleted[] = $key;
      return true;
    }
  };

  UniversalVersioning::setCacheAdapter($cache);
  UniversalVersioning::clearCache();

  expect($cache->deleted)->toContain('app_version_tag');
  expect($cache->deleted)->toContain('app_version_full');
  expect($cache->deleted)->toContain('app_version_commit');
  expect($cache->deleted)->toContain('app_version_tag-commit');
});

it('can set custom cache ttl', function () {
  UniversalVersioning::setCacheTtl(7200);

  // TTL is used internally, just verify it doesn't error
  expect(true)->toBeTrue();
});

it('handles all version formats', function () {
  $formats = ['tag', 'full', 'commit', 'tag-commit'];

  foreach ($formats as $format) {
    $version = UniversalVersioning::getVersion($format);
    expect($version)->toBeString();
  }
});

it('handles invalid repository path gracefully', function () {
  UniversalVersioning::setRepositoryPath('');

  $version = UniversalVersioning::tag();

  expect($version)->toBe('dev');
});

it('works without cache adapter', function () {
  UniversalVersioning::setCacheAdapter(null);
  UniversalVersioning::setRepositoryPath(base_path());

  $version = UniversalVersioning::tag();

  expect($version)->toBeString();
});
