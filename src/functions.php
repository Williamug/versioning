<?php

if (!function_exists('app_version')) {
  /**
   * Get the application version from git tags
   *
   * @param string $format Format type: 'tag', 'full', 'commit', 'tag-commit'
   * @return string The version string
   */
  function app_version(string $format = 'tag'): string
  {
    try {
      $repositoryPath = getcwd();

      // Validate repository path exists and is accessible
      if (!is_dir($repositoryPath) || !is_dir($repositoryPath . '/.git')) {
        return 'dev';
      }

      $escapedPath = escapeshellarg($repositoryPath);

      $command = match ($format) {
        'tag' => "git -C {$escapedPath} describe --tags --abbrev=0",
        'full' => "git -C {$escapedPath} describe --tags",
        'commit' => "git -C {$escapedPath} rev-parse --short HEAD",
        'tag-commit' => "git -C {$escapedPath} describe --tags --always",
        default => "git -C {$escapedPath} describe --tags --abbrev=0",
      };

      $output = [];
      $returnCode = 0;

      // Execute command safely
      exec($command . ' 2>&1', $output, $returnCode);

      if ($returnCode !== 0 || empty($output[0])) {
        return 'dev';
      }

      return trim($output[0]);
    } catch (\Throwable $e) {
      return 'dev';
    }
  }
}
