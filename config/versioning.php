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
