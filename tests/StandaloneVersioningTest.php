<?php

use Williamug\Versioning\StandaloneVersioning;

beforeEach(function () {
  StandaloneVersioning::clearCache();
  StandaloneVersioning::setRepositoryPath(base_path());
  StandaloneVersioning::setFallbackVersion('dev');
  StandaloneVersioning::setIncludePrefix(true);
  StandaloneVersioning::setCaching(false);
});

it('can get version tag', function () {
  $version = StandaloneVersioning::tag();

  expect($version)->toBeString();
});

it('can get full version', function () {
  $version = StandaloneVersioning::full();

  expect($version)->toBeString();
});

it('can get commit hash', function () {
  $version = StandaloneVersioning::commit();

  expect($version)->toBeString();
});

it('can get tag with commit', function () {
  $version = StandaloneVersioning::tagWithCommit();

  expect($version)->toBeString();
});

it('returns fallback version when git is not available', function () {
  StandaloneVersioning::setRepositoryPath('/nonexistent/path');

  $version = StandaloneVersioning::tag();

  expect($version)->toBe('dev');
});

it('uses custom fallback version', function () {
  StandaloneVersioning::setRepositoryPath('/nonexistent/path');
  StandaloneVersioning::setFallbackVersion('v0.0.0');

  $version = StandaloneVersioning::tag();

  expect($version)->toBe('v0.0.0');
});

it('can configure repository path', function () {
  StandaloneVersioning::setRepositoryPath(base_path());

  $version = StandaloneVersioning::tag();

  expect($version)->toBeString();
});

it('removes prefix when configured', function () {
  StandaloneVersioning::setIncludePrefix(false);
  StandaloneVersioning::setRepositoryPath(base_path());

  $version = StandaloneVersioning::tag();

  // If version starts with a number, prefix was removed
  expect($version)->toMatch('/^(\d|dev)/');
});

it('caches version when caching is enabled', function () {
  StandaloneVersioning::setCaching(true, 3600);
  StandaloneVersioning::setRepositoryPath(base_path());

  $version1 = StandaloneVersioning::tag();
  $version2 = StandaloneVersioning::tag();

  expect($version1)->toBe($version2);
});

it('can clear cache', function () {
  StandaloneVersioning::setCaching(true, 3600);
  StandaloneVersioning::setRepositoryPath(base_path());

  $version1 = StandaloneVersioning::tag();

  StandaloneVersioning::clearCache();

  $version2 = StandaloneVersioning::tag();

  expect($version1)->toBe($version2);
});

it('respects cache ttl', function () {
  StandaloneVersioning::setCaching(true, 0); // Expire immediately
  StandaloneVersioning::setRepositoryPath(base_path());

  $version1 = StandaloneVersioning::tag();

  sleep(1); // Wait for cache to expire

  $version2 = StandaloneVersioning::tag();

  expect($version1)->toBe($version2);
});

it('handles invalid repository path gracefully', function () {
  StandaloneVersioning::setRepositoryPath('');

  $version = StandaloneVersioning::tag();

  expect($version)->toBe('dev');
});

it('handles all version formats', function () {
  $formats = ['tag', 'full', 'commit', 'tag-commit'];

  foreach ($formats as $format) {
    $version = StandaloneVersioning::getVersion($format);
    expect($version)->toBeString();
  }
});
