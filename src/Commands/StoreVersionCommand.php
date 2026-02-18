<?php

namespace Williamug\Versioning\Commands;

use Illuminate\Console\Command;
use Williamug\Versioning\Models\AppVersion;

class StoreVersionCommand extends Command
{
  protected $signature = 'version:store
                            {tag : The version tag (e.g., v1.0.0)}
                            {--commit= : The commit hash}
                            {--full= : The full version string}
                            {--release= : The release name}';

  protected $description = 'Store application version in database';

  public function handle(): int
  {
    $tag = $this->argument('tag');
    $commit = $this->option('commit');
    $full = $this->option('full');
    $release = $this->option('release');

    try {
      $version = AppVersion::store(
        tag: $tag,
        full: $full,
        commit: $commit,
        releaseName: $release,
        metadata: [
          'stored_by' => 'artisan',
          'stored_at' => now()->toIso8601String(),
        ]
      );

      $this->info("✓ Version {$tag} stored successfully!");
      $this->line("  Commit: " . ($commit ?? 'N/A'));
      $this->line("  Deployed: {$version->deployed_at}");

      return self::SUCCESS;
    } catch (\Throwable $e) {
      $this->error("Failed to store version: {$e->getMessage()}");

      return self::FAILURE;
    }
  }
}
