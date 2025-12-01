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
   * Fetch version from git or version file
   */
  protected static function fetchVersion(string $format): string
  {
    try {
      $repositoryPath = Config::get('versioning.repository_path', base_path());

      // First, try to read from version file (for FTP deployments)
      $versionFromFile = self::getVersionFromFile($format, $repositoryPath);
      if ($versionFromFile !== null) {
        return $versionFromFile;
      }

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
   * Get version from static file (for FTP/non-git deployments)
   */
  protected static function getVersionFromFile(string $format, string $repositoryPath): ?string
  {
    // Check for version.txt file (simple version string)
    $versionFile = $repositoryPath . '/version.txt';
    if (file_exists($versionFile) && is_readable($versionFile)) {
      $version = trim(file_get_contents($versionFile));
      if (!empty($version)) {
        // Handle different formats
        if ($format === 'commit') {
          $commitFile = $repositoryPath . '/commit.txt';
          if (file_exists($commitFile) && is_readable($commitFile)) {
            $version = trim(file_get_contents($commitFile));
          }
        }

        // Remove 'v' prefix if configured
        if (!Config::get('versioning.include_prefix', true)) {
          $version = ltrim($version, 'v');
        }

        return $version;
      }
    }

    // Check for composer.json version
    $composerFile = $repositoryPath . '/composer.json';
    if (file_exists($composerFile) && is_readable($composerFile)) {
      $composerData = json_decode(file_get_contents($composerFile), true);
      if (isset($composerData['version'])) {
        $version = $composerData['version'];

        // Remove 'v' prefix if configured
        if (!Config::get('versioning.include_prefix', true)) {
          $version = ltrim($version, 'v');
        }

        return $version;
      }
    }

    return null;
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
