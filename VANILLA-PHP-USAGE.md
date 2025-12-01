# Vanilla PHP Quick Reference

This guide shows you how to use the Versioning package in vanilla PHP projects without Laravel.

## Installation

```bash
composer require williamug/versioning
```

## Quick Start

### Method 1: Simple Helper Function

```php
<?php
require __DIR__ . '/vendor/autoload.php';

echo app_version(); // v1.0.0
?>
```

### Method 2: Full-Featured Class

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Williamug\Versioning\StandaloneVersioning;

echo StandaloneVersioning::tag(); // v1.0.0
?>
```

## Helper Function API

```php
app_version()             // Returns: v1.0.0
app_version('tag')        // Returns: v1.0.0
app_version('full')       // Returns: v1.0.0-5-g123abc
app_version('commit')     // Returns: 123abc
app_version('tag-commit') // Returns: v1.0.0-123abc
```

## StandaloneVersioning API

### Getting Version Info

```php
use Williamug\Versioning\StandaloneVersioning;

StandaloneVersioning::tag();           // v1.0.0
StandaloneVersioning::full();          // v1.0.0-5-g123abc
StandaloneVersioning::commit();        // 123abc
StandaloneVersioning::tagWithCommit(); // v1.0.0-123abc
StandaloneVersioning::getVersion('tag'); // v1.0.0
```

### Configuration

```php
// Set repository path (default: current directory)
StandaloneVersioning::setRepositoryPath('/path/to/repo');

// Enable/disable caching and set TTL
StandaloneVersioning::setCaching(true, 3600); // Cache for 1 hour

// Set fallback version when Git is unavailable
StandaloneVersioning::setFallbackVersion('1.0.0');

// Include or remove 'v' prefix
StandaloneVersioning::setIncludePrefix(true);  // v1.0.0
StandaloneVersioning::setIncludePrefix(false); // 1.0.0

// Clear cache
StandaloneVersioning::clearCache();
```

## Common Use Cases

### Display Version in HTML Footer

```php
<footer>
    <p>Version: <?php echo app_version(); ?></p>
</footer>
```

### API Response

```php
header('Content-Type: application/json');
echo json_encode([
    'version' => app_version('tag'),
    'commit' => app_version('commit'),
    'build_date' => date('Y-m-d H:i:s')
]);
```

### Configuration File

```php
// config.php
return [
    'app_name' => 'My Application',
    'version' => app_version(),
    'debug' => false,
];
```

### About Page

```php
use Williamug\Versioning\StandaloneVersioning;

StandaloneVersioning::setCaching(true, 3600);

$versionInfo = [
    'tag' => StandaloneVersioning::tag(),
    'full' => StandaloneVersioning::full(),
    'commit' => StandaloneVersioning::commit(),
];
?>
<h1>About</h1>
<dl>
    <dt>Version:</dt>
    <dd><?php echo $versionInfo['tag']; ?></dd>

    <dt>Build:</dt>
    <dd><?php echo $versionInfo['full']; ?></dd>

    <dt>Commit:</dt>
    <dd><?php echo $versionInfo['commit']; ?></dd>
</dl>
```

### Environment-Based Fallback

```php
// Set different fallback for development vs production
$fallback = getenv('APP_ENV') === 'production' ? '1.0.0' : 'dev';
StandaloneVersioning::setFallbackVersion($fallback);

echo StandaloneVersioning::tag();
```

### Custom Repository Path

```php
// If your .git folder is not in the current directory
StandaloneVersioning::setRepositoryPath('/var/www/my-app');
echo StandaloneVersioning::tag();
```

## Error Handling

Both methods gracefully handle errors:

- Returns fallback version if Git is not installed
- Returns fallback version if not in a Git repository
- Returns fallback version if no tags exist
- Catches all exceptions automatically

```php
// Will return 'dev' if Git is unavailable
echo app_version(); // dev

// With custom fallback
StandaloneVersioning::setFallbackVersion('unknown');
echo StandaloneVersioning::tag(); // unknown
```

## Performance Tips

1. **Enable Caching**: Reduces Git command executions
   ```php
   StandaloneVersioning::setCaching(true, 3600);
   ```

2. **Set Repository Path**: Avoids repeated directory checks
   ```php
   StandaloneVersioning::setRepositoryPath(__DIR__);
   ```

3. **Clear Cache After Deployment**: Ensure fresh version info
   ```php
   StandaloneVersioning::clearCache();
   ```

## Complete Example

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Williamug\Versioning\StandaloneVersioning;

// Configure once at application bootstrap
StandaloneVersioning::setRepositoryPath(__DIR__);
StandaloneVersioning::setCaching(true, 3600);
StandaloneVersioning::setFallbackVersion('1.0.0');
StandaloneVersioning::setIncludePrefix(true);

// Use anywhere in your application
$version = StandaloneVersioning::tag();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My App v<?php echo $version; ?></title>
</head>
<body>
    <h1>Welcome to My App</h1>
    <footer>
        Version: <?php echo StandaloneVersioning::tag(); ?> |
        Build: <?php echo StandaloneVersioning::commit(); ?>
    </footer>
</body>
</html>
```

## Differences from Laravel Version

| Feature | Vanilla PHP | Laravel |
|---------|-------------|---------|
| Caching | In-memory (per request) | Laravel Cache (persistent) |
| Configuration | Static methods | Config file |
| Blade Directives | ❌ | ✅ |
| Helper Function | ✅ | ✅ |
| Class Methods | ✅ | ✅ |

## Need Laravel Features?

If you need persistent caching, Blade directives, and configuration files, use the Laravel version:

```php
use Williamug\Versioning\Versioning;

echo Versioning::tag();
```

See the main README.md for Laravel-specific documentation.
