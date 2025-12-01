# Changelog

All notable changes to `versioning` will be documented in this file.

## v3.0.0 - 2025-12-01

### Major Updates 🎉

* **BREAKING**: Dropped PHP 8.1 support, now requires PHP 8.2+
* **BREAKING**: Updated Laravel support to v10 and v11
* Added comprehensive configuration file support
* Added built-in caching mechanism for performance
* Added multiple version format options (tag, full, commit, tag-commit)
* Added proper error handling and security improvements
* Added extensive test coverage with Pest
* Added PHPStan for static analysis (level 8)
* Added additional Blade directives (@app_version_tag, @app_version_full, @app_version_commit)
* Added cache clearing functionality
* Updated CI/CD workflow with separate jobs for tests, static analysis, and code style
* Improved documentation with examples and troubleshooting
* Security: Proper command sanitization using escapeshellarg
* Security: Added fallback version support for non-git environments

### New Features

* `Versioning::full()` - Get full git describe output
* `Versioning::commit()` - Get commit hash
* `Versioning::tagWithCommit()` - Get tag with commit
* `Versioning::clearCache()` - Clear version cache
* Configuration file with extensive options
* Environment variable support for fallback version
* Configurable version prefix (v or no v)

### Developer Experience

* Added Larastan for Laravel-specific static analysis
* Updated Pint to latest version
* Added comprehensive test suite
* Improved code quality and maintainability

## v2.0.3 - 2024-04-18

### What's Changed

* Dev by @Williamug in https://github.com/Williamug/versioning/pull/13

**Full Changelog**: https://github.com/Williamug/versioning/compare/v2.0.2...v2.0.3

## v2.0.2 - 2024-04-18

### What's Changed

* Modify docs by @Williamug in https://github.com/Williamug/versioning/pull/12

**Full Changelog**: https://github.com/Williamug/versioning/compare/v2.0.1...v2.0.2

## v2.0.1 - 2024-04-16

### What's Changed

* Fix bug by @Williamug in https://github.com/Williamug/versioning/pull/11

**Full Changelog**: https://github.com/Williamug/versioning/compare/v2.0.0...v2.0.1

## v2.0.0 - 2024-04-16

### What's Changed

* Add a blade directive by @Williamug in https://github.com/Williamug/versioning/pull/10

**Full Changelog**: https://github.com/Williamug/versioning/compare/v1.0.4...v2.0.0

## v1.0.4 - 2024-04-10

### What's Changed

* wip by @Williamug in https://github.com/Williamug/versioning/pull/9

**Full Changelog**: https://github.com/Williamug/versioning/compare/v1.0.3...v1.0.4

## v1.0.3 - 2024-04-10

### What's Changed

* Add more badges by @Williamug in https://github.com/Williamug/versioning/pull/8

**Full Changelog**: https://github.com/Williamug/versioning/compare/v1.0.2...v1.0.3

## v1.0.2 - 2024-04-10

### What's Changed

* Update badge by @Williamug in https://github.com/Williamug/versioning/pull/7

**Full Changelog**: https://github.com/Williamug/versioning/compare/v1.0.1...v1.0.2

## v1.0.1 - 2024-04-10

### What's Changed

* wip by @Williamug in https://github.com/Williamug/versioning/pull/6

**Full Changelog**: https://github.com/Williamug/versioning/compare/v1.0.0...v1.0.1

## v1.0.0-beta.2 - 2024-04-10

### What's Changed

* Add upates by @Williamug in https://github.com/Williamug/versioning/pull/3

**Full Changelog**: https://github.com/Williamug/versioning/compare/v1.0.0-beta.1...v1.0.0-beta.2

## v1.0.0-beta.1 - 2024-04-10

### What's Changed

* First PR by @Williamug in https://github.com/Williamug/versioning/pull/2

### New Contributors

* @Williamug made their first contribution in https://github.com/Williamug/versioning/pull/2

**Full Changelog**: https://github.com/Williamug/versioning/commits/v1.0.0-beta.1
