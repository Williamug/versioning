<?php

namespace Williamug\Versioning;

/**
 * Universal Versioning class that works with any PHP framework
 * Supports Laravel, Symfony, CodeIgniter, CakePHP, Slim, and more
 */
class UniversalVersioning
{
  protected static ?string $repositoryPath = null;
  protected static ?object $cacheAdapter = null;
  protected static string $fallbackVersion = 'dev';
  protected static bool $includePrefix = true;
  protected static int $cacheTtl = 3600;

  /**
   * Set repository path
   */
  public static function setRepositoryPath(string $path): void
  {
    self::$repositoryPath = $path;
  }

  /**
   * Set cache adapter (works with any PSR-6 or PSR-16 cache)
   */
  public static function setCacheAdapter(?object $cache): void
  {
    self::$cacheAdapter = $cache;
  }

  /**
   * Set fallback version
   */
  public static function setFallbackVersion(string $version): void
  {
    self::$fallbackVersion = $version;
  }

  /**
   * Set whether to include 'v' prefix
   */
  public static function setIncludePrefix(bool $include): void
  {
    self::$includePrefix = $include;
  }

  /**
   * Set cache TTL in seconds
   */
  public static function setCacheTtl(int $ttl): void
  {
    self::$cacheTtl = $ttl;
  }

  /**
   * Get the current version tag
   */
  public static function tag(): string
  {
    return self::getVersion('tag');
  }

  /**
   * Get the full version information
   */
  public static function full(): string
  {
    return self::getVersion('full');
  }

  /**
   * Get the commit hash
   */
  public static function commit(): string
  {
    return self::getVersion('commit');
  }

  /**
   * Get version with commit hash
   */
  public static function tagWithCommit(): string
  {
    return self::getVersion('tag-commit');
  }

  /**
   * Get version based on format
   */
  public static function getVersion(string $format = 'tag'): string
  {
    $cacheKey = "app_version_{$format}";

    // Try to get from cache
    $cachedVersion = self::getFromCache($cacheKey);
    if ($cachedVersion !== null) {
      return $cachedVersion;
    }

    // Fetch fresh version
    $version = self::fetchVersion($format);

    // Store in cache
    self::storeInCache($cacheKey, $version);

    return $version;
  }

  /**
   * Fetch version from git
   */
  protected static function fetchVersion(string $format): string
  {
    try {
      $repositoryPath = self::$repositoryPath ?? getcwd();

      if (!is_dir($repositoryPath) || !is_dir($repositoryPath . '/.git')) {
        return self::$fallbackVersion;
      }

      $command = self::buildGitCommand($format, $repositoryPath);
      $output = [];
      $returnCode = 0;

      exec($command . ' 2>&1', $output, $returnCode);

      if ($returnCode !== 0 || empty($output[0])) {
        return self::$fallbackVersion;
      }

      $version = trim($output[0]);

      if (!self::$includePrefix) {
        $version = ltrim($version, 'v');
      }

      return $version;
    } catch (\Throwable $e) {
      return self::$fallbackVersion;
    }
  }

  /**
   * Build git command based on format
   */
  protected static function buildGitCommand(string $format, string $repositoryPath): string
  {
    $escapedPath = escapeshellarg($repositoryPath);

    return match ($format) {
      'tag' => "git -C {$escapedPath} describe --tags --abbrev=0",
      'full' => "git -C {$escapedPath} describe --tags",
      'commit' => "git -C {$escapedPath} rev-parse --short HEAD",
      'tag-commit' => "git -C {$escapedPath} describe --tags --always",
      default => "git -C {$escapedPath} describe --tags --abbrev=0",
    };
  }

  /**
   * Get value from cache (supports multiple cache systems)
   */
  protected static function getFromCache(string $key): ?string
  {
    if (self::$cacheAdapter === null) {
      return null;
    }

    try {
      // PSR-16 SimpleCache (Symfony, Laravel 5.8+)
      if (method_exists(self::$cacheAdapter, 'get')) {
        $value = self::$cacheAdapter->get($key);
        return $value !== null ? (string) $value : null;
      }

      // PSR-6 Cache (Symfony)
      if (method_exists(self::$cacheAdapter, 'getItem')) {
        $item = self::$cacheAdapter->getItem($key);
        return $item->isHit() ? (string) $item->get() : null;
      }

      // Laravel Cache
      if (method_exists(self::$cacheAdapter, 'has')) {
        return self::$cacheAdapter->has($key) ? self::$cacheAdapter->get($key) : null;
      }

      // CodeIgniter Cache
      if (method_exists(self::$cacheAdapter, 'getMetadata')) {
        return self::$cacheAdapter->get($key) ?: null;
      }
    } catch (\Throwable $e) {
      // Cache failure shouldn't break the application
      return null;
    }

    return null;
  }

  /**
   * Store value in cache (supports multiple cache systems)
   */
  protected static function storeInCache(string $key, string $value): void
  {
    if (self::$cacheAdapter === null) {
      return;
    }

    try {
      // PSR-16 SimpleCache (Symfony, Laravel 5.8+)
      if (method_exists(self::$cacheAdapter, 'set')) {
        self::$cacheAdapter->set($key, $value, self::$cacheTtl);
        return;
      }

      // PSR-6 Cache (Symfony)
      if (method_exists(self::$cacheAdapter, 'getItem')) {
        $item = self::$cacheAdapter->getItem($key);
        $item->set($value);
        $item->expiresAfter(self::$cacheTtl);
        self::$cacheAdapter->save($item);
        return;
      }

      // Laravel Cache
      if (method_exists(self::$cacheAdapter, 'put')) {
        self::$cacheAdapter->put($key, $value, self::$cacheTtl);
        return;
      }

      // CodeIgniter Cache
      if (method_exists(self::$cacheAdapter, 'save')) {
        self::$cacheAdapter->save($key, $value, self::$cacheTtl);
        return;
      }
    } catch (\Throwable $e) {
      // Cache failure shouldn't break the application
    }
  }

  /**
   * Clear version cache
   */
  public static function clearCache(): void
  {
    if (self::$cacheAdapter === null) {
      return;
    }

    $formats = ['tag', 'full', 'commit', 'tag-commit'];

    foreach ($formats as $format) {
      $key = "app_version_{$format}";

      try {
        // PSR-16 SimpleCache
        if (method_exists(self::$cacheAdapter, 'delete')) {
          self::$cacheAdapter->delete($key);
          continue;
        }

        // PSR-6 Cache
        if (method_exists(self::$cacheAdapter, 'deleteItem')) {
          self::$cacheAdapter->deleteItem($key);
          continue;
        }

        // Laravel Cache
        if (method_exists(self::$cacheAdapter, 'forget')) {
          self::$cacheAdapter->forget($key);
          continue;
        }

        // CodeIgniter Cache
        if (method_exists(self::$cacheAdapter, 'delete')) {
          self::$cacheAdapter->delete($key);
          continue;
        }
      } catch (\Throwable $e) {
        // Continue clearing other keys
      }
    }
  }
}
