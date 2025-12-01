<?php

/**
 * Vanilla PHP Example
 *
 * This example demonstrates how to use the versioning package
 * in a vanilla PHP project without Laravel.
 */

require __DIR__.'/../vendor/autoload.php';

echo "=== Versioning Package - Vanilla PHP Example ===\n\n";

// Method 1: Using the helper function (simplest)
echo "Method 1: Helper Function\n";
echo "-------------------------\n";
echo 'Tag version:    '.app_version('tag')."\n";
echo 'Full version:   '.app_version('full')."\n";
echo 'Commit hash:    '.app_version('commit')."\n";
echo 'Tag + commit:   '.app_version('tag-commit')."\n";
echo "\n";

// Method 2: Using the StandaloneVersioning class (more features)
echo "Method 2: Standalone Class\n";
echo "--------------------------\n";

use Williamug\Versioning\StandaloneVersioning;

// Optional configuration
StandaloneVersioning::setRepositoryPath(__DIR__.'/..');
StandaloneVersioning::setFallbackVersion('dev-master');
StandaloneVersioning::setCaching(true, 3600); // Cache for 1 hour
StandaloneVersioning::setIncludePrefix(true); // Include 'v' prefix

echo 'Tag version:    '.StandaloneVersioning::tag()."\n";
echo 'Full version:   '.StandaloneVersioning::full()."\n";
echo 'Commit hash:    '.StandaloneVersioning::commit()."\n";
echo 'Tag + commit:   '.StandaloneVersioning::tagWithCommit()."\n";
echo "\n";

// Demonstrate configuration options
echo "Configuration Examples\n";
echo "---------------------\n";

// Without 'v' prefix
StandaloneVersioning::setIncludePrefix(false);
echo 'Without prefix: '.StandaloneVersioning::tag()."\n";

// With custom fallback
StandaloneVersioning::setRepositoryPath('/nonexistent/path');
echo 'Custom fallback: '.StandaloneVersioning::tag()."\n";

// Clear cache
StandaloneVersioning::clearCache();
echo "Cache cleared!\n";
echo "\n";

// HTML example
echo "HTML Integration Example\n";
echo "------------------------\n";
?>
<!DOCTYPE html>
<html>

<head>
  <title>My App</title>
</head>

<body>
  <footer>
    <p>Version: <?php echo app_version(); ?></p>
    <p>Build: <?php echo app_version('commit'); ?></p>
  </footer>
</body>

</html>
<?php

echo "\nDone!\n";
