# Which Class Should I Use?

Choose the right versioning class for your project:

## Quick Comparison

| Class | Best For | Cache Support | Configuration | Laravel Features |
|-------|----------|---------------|---------------|-----------------|
| **UniversalVersioning** | Any framework with cache | PSR-6, PSR-16, framework-specific | Static methods | ❌ |
| **StandaloneVersioning** | Vanilla PHP, no cache | In-memory (per-request) | Static methods | ❌ |
| **Versioning** | Laravel projects | Laravel Cache (persistent) | Config file | ✅ Blade, Facades |

## Decision Tree

```
Do you use Laravel?
├─ Yes → Use Versioning class (full Laravel integration)
│
└─ No → Do you have a cache system?
    ├─ Yes → Use UniversalVersioning (works with any cache)
    └─ No → Use StandaloneVersioning (works without cache)
```

## Detailed Breakdown

### UniversalVersioning

**Use when:**
- Using Symfony, CodeIgniter, CakePHP, Slim, Yii2, Laminas, or Phalcon
- Have PSR-6 or PSR-16 compatible cache
- Want persistent caching across requests
- Working with any modern PHP framework

**Features:**
- Auto-detects cache interface (PSR-6/PSR-16)
- Works with any framework's cache system
- Persistent caching across requests
- Full configuration via static methods

**Example:**
```php
use Williamug\Versioning\UniversalVersioning;

UniversalVersioning::setRepositoryPath(__DIR__);
UniversalVersioning::setCacheAdapter($yourFrameworkCache);
UniversalVersioning::setFallbackVersion('1.0.0');

echo UniversalVersioning::tag(); // v1.0.0
```

---

### StandaloneVersioning

**Use when:**
- Building vanilla PHP applications
- No cache system available
- Prototyping or simple projects
- Don't need persistent caching

**Features:**
- Zero dependencies (no framework, no cache)
- In-memory caching (per PHP request)
- Simple static method configuration
- Works anywhere PHP runs

**Example:**
```php
use Williamug\Versioning\StandaloneVersioning;

StandaloneVersioning::setRepositoryPath(__DIR__);
StandaloneVersioning::setCaching(true, 3600);
StandaloneVersioning::setFallbackVersion('1.0.0');

echo StandaloneVersioning::tag(); // v1.0.0
```

---

### Versioning (Laravel)

**Use when:**
- Working with Laravel 10+ projects
- Want config file integration
- Need Blade directives
- Want facade support

**Features:**
- Full Laravel integration
- Config file: `config/versioning.php`
- Blade directives: `@app_version_tag`, etc.
- Facade support: `Versioning::tag()`
- Laravel Cache integration (Redis, File, etc.)
- Service provider auto-registration

**Example:**
```php
use Williamug\Versioning\Versioning;

echo Versioning::tag(); // v1.0.0
```

**Blade:**
```blade
<footer>Version: @app_version_tag</footer>
```

---

## Feature Comparison

| Feature | Universal | Standalone | Laravel |
|---------|-----------|------------|---------|
| **Caching** | |||
| Persistent cache | ✅ | ❌ | ✅ |
| In-memory cache | ❌ | ✅ | ❌ |
| PSR-6 support | ✅ | ❌ | ✅ |
| PSR-16 support | ✅ | ❌ | ✅ |
| **Configuration** | |||
| Static methods | ✅ | ✅ | ❌ |
| Config file | ❌ | ❌ | ✅ |
| Environment vars | ✅ | ✅ | ✅ |
| **Framework Integration** | |||
| Vanilla PHP | ✅ | ✅ | ❌ |
| Symfony | ✅ | ❌ | ❌ |
| CodeIgniter | ✅ | ❌ | ❌ |
| CakePHP | ✅ | ❌ | ❌ |
| Slim | ✅ | ❌ | ❌ |
| Laravel | ✅ | ❌ | ✅ |
| Yii2 | ✅ | ❌ | ❌ |
| Laminas | ✅ | ❌ | ❌ |
| Phalcon | ✅ | ❌ | ❌ |
| **Laravel Features** | |||
| Blade directives | ❌ | ❌ | ✅ |
| Facades | ❌ | ❌ | ✅ |
| Service provider | ❌ | ❌ | ✅ |
| Config publishing | ❌ | ❌ | ✅ |
| **Version Formats** | |||
| Tag | ✅ | ✅ | ✅ |
| Full | ✅ | ✅ | ✅ |
| Commit | ✅ | ✅ | ✅ |
| Tag + commit | ✅ | ✅ | ✅ |
| **Error Handling** | |||
| Fallback version | ✅ | ✅ | ✅ |
| Exception handling | ✅ | ✅ | ✅ |
| Graceful degradation | ✅ | ✅ | ✅ |

---

## Common Scenarios

### Scenario 1: Symfony Project
**Use:** `UniversalVersioning`
```php
UniversalVersioning::setCacheAdapter($cache); // Symfony Cache
```

### Scenario 2: Simple PHP Website
**Use:** `StandaloneVersioning`
```php
echo StandaloneVersioning::tag();
```

### Scenario 3: Laravel API
**Use:** `Versioning` (Laravel class)
```php
return ['version' => Versioning::tag()];
```

### Scenario 4: CodeIgniter App
**Use:** `UniversalVersioning`
```php
UniversalVersioning::setCacheAdapter(\Config\Services::cache());
```

### Scenario 5: Legacy PHP (No Framework)
**Use:** `StandaloneVersioning` or helper function
```php
echo app_version(); // Simple helper
```

---

## Migration Between Classes

### From StandaloneVersioning → UniversalVersioning

```php
// Before
StandaloneVersioning::tag();

// After (add cache support)
UniversalVersioning::setCacheAdapter($cache);
UniversalVersioning::tag();
```

### From UniversalVersioning → Versioning (Laravel)

```php
// Before
UniversalVersioning::tag();

// After (use Laravel class)
Versioning::tag();

// Or in Blade
@app_version_tag
```

---

## Still Not Sure?

- **Just getting started?** Use `app_version()` helper function
- **Need simple solution?** Use `StandaloneVersioning`
- **Have a cache system?** Use `UniversalVersioning`
- **Using Laravel?** Use `Versioning` class with Blade directives

All classes provide the same core functionality - just pick the one that fits your project best! 🚀
