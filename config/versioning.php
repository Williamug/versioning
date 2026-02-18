<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Git Repository Path
    |--------------------------------------------------------------------------
    |
    | The path to your git repository. By default, it uses the base path
    | of your Laravel application. You can customize this if your .git
    | directory is located elsewhere.
    |
    */
  'repository_path' => base_path(),

  /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Enable caching to improve performance by reducing git command executions.
    | Cache TTL is specified in seconds.
    |
    */
  'cache' => [
    'enabled' => env('VERSIONING_CACHE_ENABLED', true),
    'ttl' => env('VERSIONING_CACHE_TTL', 3600), // 1 hour
    'key' => 'app_version',
  ],

  /*
    |--------------------------------------------------------------------------
    | Fallback Version
    |--------------------------------------------------------------------------
    |
    | The version to display when git information is unavailable or
    | when an error occurs during version retrieval.
    |
    */
  'fallback_version' => env('APP_VERSION', 'dev'),

  /*
    |--------------------------------------------------------------------------
    | Database Storage (Optional)
    |--------------------------------------------------------------------------
    |
    | Enable database storage for version information. This is useful for
    | FTP deployments where .git directory is not available. When enabled,
    | the package will check the database first before falling back to
    | version files or git.
    |
    | Auto-sync: When enabled, the package will automatically sync version
    | from version.txt to database on first request after deployment.
    | This eliminates the need for manual commands or API calls.
    |
    | To use this feature:
    | 1. Publish migration: php artisan vendor:publish --tag="versioning-migrations"
    | 2. Run migration: php artisan migrate
    | 3. Enable auto_sync (recommended) or manually store version
    |
    */
  'use_database' => env('VERSIONING_USE_DATABASE', false),
  'auto_sync' => env('VERSIONING_AUTO_SYNC', true),
  'model' => \Williamug\Versioning\Models\AppVersion::class,

  /*
    |--------------------------------------------------------------------------
    | Version Format
    |--------------------------------------------------------------------------
    |
    | Customize how the version is displayed:
    | - 'tag' - Show only the tag (e.g., v1.0.0)
    | - 'tag-commit' - Show tag with commit hash (e.g., v1.0.0-abc1234)
    | - 'full' - Show full git describe output (e.g., v1.0.0-5-abc1234)
    |
    */
  'format' => env('VERSIONING_FORMAT', 'tag'),

  /*
    |--------------------------------------------------------------------------
    | Include Prefix
    |--------------------------------------------------------------------------
    |
    | Whether to include the 'v' prefix in version numbers.
    | Set to false to display '1.0.0' instead of 'v1.0.0'
    |
    */
  'include_prefix' => true,
];
