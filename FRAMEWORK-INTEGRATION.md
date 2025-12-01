# Framework Integration Guide

This package works seamlessly with **any PHP framework**. This guide shows you how to integrate it with popular frameworks.

## Quick Overview

The package provides three classes for different use cases:

| Class | Best For | Dependencies |
|-------|----------|--------------|
| `UniversalVersioning` | Any framework with cache | None (auto-detects cache) |
| `StandaloneVersioning` | Vanilla PHP / No cache | None |
| `Versioning` | Laravel projects | Laravel facades |

## Universal Integration

The `UniversalVersioning` class auto-detects and works with:
- PSR-6 Cache (CacheItemPoolInterface)
- PSR-16 Simple Cache (CacheInterface)
- Laravel Cache
- Symfony Cache
- CodeIgniter Cache
- Any cache system with `get/set` methods

### Basic Setup (Any Framework)

```php
use Williamug\Versioning\UniversalVersioning;

// Configure once in your bootstrap/config
UniversalVersioning::setRepositoryPath(__DIR__);
UniversalVersioning::setCacheAdapter($yourCacheInstance);
UniversalVersioning::setFallbackVersion('1.0.0');
UniversalVersioning::setCacheTtl(3600); // 1 hour

// Use anywhere
echo UniversalVersioning::tag(); // v1.0.0
```

---

## Laravel

### Method 1: Using Built-in Integration

```php
use Williamug\Versioning\Versioning;

// In controllers
$version = Versioning::tag();

// In Blade templates
@app_version_tag
@app_version_full
@app_version_commit
```

### Method 2: Using Universal Class

```php
use Illuminate\Support\Facades\Cache;
use Williamug\Versioning\UniversalVersioning;

UniversalVersioning::setRepositoryPath(base_path());
UniversalVersioning::setCacheAdapter(Cache::getFacadeRoot());

$version = UniversalVersioning::tag();
```

**Configuration:** Publish config with `php artisan vendor:publish --tag="versioning-config"`

---

## Symfony

### Setup

```php
// src/Service/VersioningService.php
namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Williamug\Versioning\UniversalVersioning;

class VersioningService
{
    public function __construct(
        private CacheInterface $cache,
        private string $projectDir
    ) {
        UniversalVersioning::setRepositoryPath($this->projectDir);
        UniversalVersioning::setCacheAdapter($this->cache);
        UniversalVersioning::setFallbackVersion($_ENV['APP_VERSION'] ?? 'dev');
    }

    public function getVersion(): string
    {
        return UniversalVersioning::tag();
    }
}
```

### Configuration (config/services.yaml)

```yaml
services:
    App\Service\VersioningService:
        arguments:
            $cache: '@cache.app'
            $projectDir: '%kernel.project_dir%'
```

### Usage in Controllers

```php
use App\Service\VersioningService;

#[Route('/')]
public function index(VersioningService $versioning): Response
{
    return $this->render('index.html.twig', [
        'version' => $versioning->getVersion(),
    ]);
}
```

### Usage in Twig

```twig
{# Register as global in src/Twig/AppExtension.php #}
<footer>Version: {{ app_version }}</footer>
```

**Full example:** See `examples/symfony-integration.php`

---

## CodeIgniter 4

### Setup

```php
// app/Libraries/Versioning.php
namespace App\Libraries;

use Williamug\Versioning\UniversalVersioning;

class Versioning
{
    public function __construct()
    {
        $cache = \Config\Services::cache();

        UniversalVersioning::setRepositoryPath(ROOTPATH);
        UniversalVersioning::setCacheAdapter($cache);
        UniversalVersioning::setFallbackVersion(env('app.version', 'dev'));
    }

    public function getVersion(): string
    {
        return UniversalVersioning::tag();
    }
}
```

### Create Helper

```php
// app/Helpers/version_helper.php
if (!function_exists('get_app_version')) {
    function get_app_version(): string
    {
        $versioning = new \App\Libraries\Versioning();
        return $versioning->getVersion();
    }
}
```

### Load in Autoload

```php
// app/Config/Autoload.php
public $helpers = ['version'];
```

### Usage

```php
// In controllers
$versioning = new \App\Libraries\Versioning();
$data['version'] = $versioning->getVersion();

// In views
<?= get_app_version() ?>
```

**Full example:** See `examples/codeigniter-integration.php`

---

## CakePHP 5

### Create Component

```php
// src/Controller/Component/VersioningComponent.php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Cache\Cache;
use Williamug\Versioning\UniversalVersioning;

class VersioningComponent extends Component
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        UniversalVersioning::setRepositoryPath(ROOT);
        UniversalVersioning::setCacheAdapter(Cache::pool('default'));
    }

    public function getVersion(): string
    {
        return UniversalVersioning::tag();
    }
}
```

### Load in AppController

```php
public function initialize(): void
{
    parent::initialize();
    $this->loadComponent('Versioning');
}
```

### Usage

```php
// In controllers
$version = $this->Versioning->getVersion();

// In views (after creating helper)
<?= $this->Version->tag() ?>
```

**Full example:** See `examples/cakephp-integration.php`

---

## Slim Framework 4

### Bootstrap Setup

```php
use Williamug\Versioning\UniversalVersioning;

// Configure in bootstrap
UniversalVersioning::setRepositoryPath(__DIR__ . '/..');
UniversalVersioning::setFallbackVersion(getenv('APP_VERSION') ?: 'dev');

// Optional: Add PSR-16 cache
UniversalVersioning::setCacheAdapter($container->get('cache'));
```

### Usage in Routes

```php
$app->get('/', function ($request, $response) {
    $version = UniversalVersioning::tag();
    $response->getBody()->write("Version: {$version}");
    return $response;
});

$app->get('/api/version', function ($request, $response) {
    $data = [
        'version' => UniversalVersioning::tag(),
        'commit' => UniversalVersioning::commit(),
    ];
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});
```

### With Twig

```php
$twig->getEnvironment()->addGlobal('app_version', UniversalVersioning::tag());
```

**Full example:** See `examples/slim-integration.php`

---

## Laminas (Zend Framework)

### Setup

```php
// module/Application/src/Service/VersioningService.php
namespace Application\Service;

use Laminas\Cache\Storage\StorageInterface;
use Williamug\Versioning\UniversalVersioning;

class VersioningService
{
    public function __construct(private StorageInterface $cache)
    {
        UniversalVersioning::setRepositoryPath(getcwd());
        UniversalVersioning::setCacheAdapter($this->cache);
    }

    public function getVersion(): string
    {
        return UniversalVersioning::tag();
    }
}
```

### Factory

```php
// module/Application/src/Service/Factory/VersioningServiceFactory.php
namespace Application\Service\Factory;

use Application\Service\VersioningService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class VersioningServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new VersioningService($container->get('cache'));
    }
}
```

### Configure in module.config.php

```php
'service_manager' => [
    'factories' => [
        \Application\Service\VersioningService::class =>
            \Application\Service\Factory\VersioningServiceFactory::class,
    ],
],
```

---

## Yii2

### Setup

```php
// common/components/Versioning.php
namespace common\components;

use yii\base\Component;
use Williamug\Versioning\UniversalVersioning;

class Versioning extends Component
{
    public function init()
    {
        parent::init();

        UniversalVersioning::setRepositoryPath(\Yii::getAlias('@app'));
        UniversalVersioning::setCacheAdapter(\Yii::$app->cache);
        UniversalVersioning::setFallbackVersion(\Yii::$app->params['version'] ?? 'dev');
    }

    public function getVersion(): string
    {
        return UniversalVersioning::tag();
    }

    public function getCommit(): string
    {
        return UniversalVersioning::commit();
    }
}
```

### Configure in config/web.php

```php
'components' => [
    'versioning' => [
        'class' => 'common\components\Versioning',
    ],
],
```

### Usage

```php
// In controllers
$version = Yii::$app->versioning->getVersion();

// In views
<?= Yii::$app->versioning->getVersion() ?>
```

---

## Phalcon

### Setup

```php
// app/library/Versioning.php
use Williamug\Versioning\UniversalVersioning;

class Versioning
{
    protected $di;

    public function __construct($di)
    {
        $this->di = $di;

        UniversalVersioning::setRepositoryPath(BASE_PATH);
        UniversalVersioning::setCacheAdapter($di->get('cache'));
    }

    public function getVersion(): string
    {
        return UniversalVersioning::tag();
    }
}
```

### Register in DI

```php
$di->setShared('versioning', function () use ($di) {
    return new Versioning($di);
});
```

### Usage

```php
// In controllers
$version = $this->di->get('versioning')->getVersion();

// In Volt templates
{{ di.get('versioning').getVersion() }}
```

---

## Custom Framework / Legacy PHP

### Without Cache

```php
use Williamug\Versioning\StandaloneVersioning;

StandaloneVersioning::setRepositoryPath(__DIR__);
StandaloneVersioning::setFallbackVersion('1.0.0');

echo StandaloneVersioning::tag();
```

### With Custom Cache

```php
use Williamug\Versioning\UniversalVersioning;

// Your custom cache class
class MyCache {
    public function get($key) { /* ... */ }
    public function set($key, $value, $ttl) { /* ... */ }
}

UniversalVersioning::setCacheAdapter(new MyCache());
echo UniversalVersioning::tag();
```

---

## Cache Adapter Requirements

The `UniversalVersioning` class automatically detects and works with caches that implement:

### PSR-16 Simple Cache
```php
interface SimpleCacheInterface {
    public function get($key, $default = null);
    public function set($key, $value, $ttl = null);
    public function delete($key);
}
```

### PSR-6 Cache
```php
interface CacheItemPoolInterface {
    public function getItem($key);
    public function save(CacheItemInterface $item);
    public function deleteItem($key);
}
```

### Basic Cache Interface
Any object with these methods:
```php
public function get($key);
public function set($key, $value, $ttl);
public function delete($key);
```

---

## Environment Variables

All frameworks can use environment variables:

```bash
# .env file
APP_VERSION=1.0.0
VERSIONING_CACHE_TTL=3600
```

```php
UniversalVersioning::setFallbackVersion(getenv('APP_VERSION') ?: 'dev');
UniversalVersioning::setCacheTtl((int) getenv('VERSIONING_CACHE_TTL') ?: 3600);
```

---

## API Response Example (Any Framework)

```php
header('Content-Type: application/json');

echo json_encode([
    'app' => 'My Application',
    'version' => UniversalVersioning::tag(),
    'build' => UniversalVersioning::commit(),
    'timestamp' => time(),
]);
```

---

## Troubleshooting

### Cache Not Working

```php
// Test if cache is configured
$cache = $yourCacheInstance;
var_dump(method_exists($cache, 'get')); // Should be true
var_dump(method_exists($cache, 'set')); // Should be true
```

### Version Not Updating

```php
// Clear cache after deployment
UniversalVersioning::clearCache();
```

### Git Not Found

```php
// Set explicit repository path
UniversalVersioning::setRepositoryPath('/absolute/path/to/repo');

// Set fallback version
UniversalVersioning::setFallbackVersion('1.0.0');
```

---

## Need Help?

- See working examples in the `examples/` directory
- Check framework-specific documentation
- Open an issue on GitHub

The package is designed to work with **any PHP framework** out of the box! 🚀
