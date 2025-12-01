<?php

namespace Williamug\Versioning;

/**
 * Standalone Versioning class for vanilla PHP
 * No Laravel dependencies required
 */
class StandaloneVersioning
{
  protected static ?string $repositoryPath = null;
  protected static array $cache = [];
  protected static bool $cacheEnabled = true;
  protected static int $cacheTtl = 3600;
  protected static string $fallbackVersion = 'dev';
  protected static bool $includePrefix = true;

  /**
   * Set repository path
   */
  public static function setRepositoryPath(string $path): void
  {
    self::$repositoryPath = $path;
  }

  /**
   * Configure caching
   */
  public static function setCaching(bool $enabled, int $ttl = 3600): void
  {
    self::$cacheEnabled = $enabled;
    self::$cacheTtl = $ttl;
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
    $cacheKey = "version_{$format}";

    // Check cache
    if (self::$cacheEnabled && isset(self::$cache[$cacheKey])) {
      $cached = self::$cache[$cacheKey];
      if (time() - $cached['time'] < self::$cacheTtl) {
        return $cached['value'];
      }
    }

    $version = self::fetchVersion($format);

    // Store in cache
    if (self::$cacheEnabled) {
      self::$cache[$cacheKey] = [
        'value' => $version,
        'time' => time(),
      ];
    }

    return $version;
  }

  /**
   * Fetch version from git
   */
  protected static function fetchVersion(string $format): string
  {
    try {
      $repositoryPath = self::$repositoryPath ?? getcwd();

      // Validate repository path exists and is accessible
      if (!is_dir($repositoryPath) || !is_dir($repositoryPath . '/.git')) {
        return self::$fallbackVersion;
      }

      $command = self::buildGitCommand($format, $repositoryPath);
      $output = [];
      $returnCode = 0;

      // Execute command safely
      exec($command . ' 2>&1', $output, $returnCode);

      if ($returnCode !== 0 || empty($output[0])) {
        return self::$fallbackVersion;
      }

      $version = trim($output[0]);

      // Remove 'v' prefix if configured
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
   * Clear version cache
   */
  public static function clearCache(): void
  {
    self::$cache = [];
  }
}
