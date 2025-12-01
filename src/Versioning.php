<?php

namespace Williamug\Versioning;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class Versioning
{
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
    $cacheEnabled = Config::get('versioning.cache.enabled', true);
    $cacheKey = Config::get('versioning.cache.key', 'app_version') . "_{$format}";
    $cacheTtl = Config::get('versioning.cache.ttl', 3600);

    if ($cacheEnabled && Cache::has($cacheKey)) {
      return Cache::get($cacheKey);
    }

    $version = self::fetchVersion($format);

    if ($cacheEnabled) {
      Cache::put($cacheKey, $version, $cacheTtl);
    }

    return $version;
  }

  /**
   * Fetch version from git
   */
  protected static function fetchVersion(string $format): string
  {
    try {
      $repositoryPath = Config::get('versioning.repository_path', base_path());

      // Validate repository path exists and is accessible
      if (!is_dir($repositoryPath) || !is_dir($repositoryPath . '/.git')) {
        return self::getFallbackVersion();
      }

      $command = self::buildGitCommand($format, $repositoryPath);
      $output = [];
      $returnCode = 0;

      // Execute command safely
      exec($command . ' 2>&1', $output, $returnCode);

      if ($returnCode !== 0 || empty($output[0])) {
        return self::getFallbackVersion();
      }

      $version = trim($output[0]);

      // Remove 'v' prefix if configured
      if (!Config::get('versioning.include_prefix', true)) {
        $version = ltrim($version, 'v');
      }

      return $version;
    } catch (\Throwable $e) {
      return self::getFallbackVersion();
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
   * Get fallback version from config
   */
  protected static function getFallbackVersion(): string
  {
    return Config::get('versioning.fallback_version', 'dev');
  }

  /**
   * Clear version cache
   */
  public static function clearCache(): void
  {
    $cacheKey = Config::get('versioning.cache.key', 'app_version');
    $formats = ['tag', 'full', 'commit', 'tag-commit'];

    foreach ($formats as $format) {
      Cache::forget("{$cacheKey}_{$format}");
    }
  }
}
