<?php

namespace Williamug\Versioning\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Williamug\Versioning\Models\AppVersion;

class AutoSyncVersionFromFile
{
  /**
   * Handle an incoming request and auto-sync version from files to database
   */
  public function handle(Request $request, Closure $next)
  {
    // Only run once per deployment using cache lock
    $lockKey = 'versioning_auto_sync_lock';

    if (!Cache::has($lockKey)) {
      $this->syncVersionToDatabase();

      // Lock for 1 hour to prevent repeated checks
      Cache::put($lockKey, true, 3600);
    }

    return $next($request);
  }

  /**
   * Sync version from files to database
   */
  protected function syncVersionToDatabase(): void
  {
    try {
      $basePath = base_path();
      $versionFile = $basePath . '/version.txt';
      $commitFile = $basePath . '/commit.txt';

      // Check if version files exist
      if (!file_exists($versionFile)) {
        return;
      }

      $tag = trim(file_get_contents($versionFile));
      $commit = file_exists($commitFile) ? trim(file_get_contents($commitFile)) : null;

      if (empty($tag)) {
        return;
      }

      // Check if this version is already stored
      $current = AppVersion::current();

      if ($current && $current->version_tag === $tag && $current->commit_hash === $commit) {
        return; // Already up to date
      }

      // Store new version
      AppVersion::store(
        tag: $tag,
        commit: $commit,
        metadata: [
          'auto_synced' => true,
          'synced_at' => now()->toIso8601String(),
        ]
      );

      \Log::info("Auto-synced version to database: {$tag}");
    } catch (\Throwable $e) {
      // Silently fail - don't break the application
      \Log::error("Failed to auto-sync version: {$e->getMessage()}");
    }
  }
}
