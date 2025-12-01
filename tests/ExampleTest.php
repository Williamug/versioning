<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Williamug\Versioning\Versioning;

beforeEach(function () {
  Config::set('versioning.repository_path', base_path());
  Config::set('versioning.cache.enabled', false);
  Config::set('versioning.fallback_version', 'dev');
  Config::set('versioning.include_prefix', true);
});

it('returns fallback version when git is not available', function () {
  Config::set('versioning.repository_path', '/nonexistent/path');

  $version = Versioning::tag();

  expect($version)->toBe('dev');
});

it('returns fallback version when not in a git repository', function () {
  Config::set('versioning.repository_path', sys_get_temp_dir());

  $version = Versioning::tag();

  expect($version)->toBe('dev');
});

it('uses custom fallback version from config', function () {
  Config::set('versioning.repository_path', '/nonexistent/path');
  Config::set('versioning.fallback_version', 'v0.0.0');

  $version = Versioning::tag();

  expect($version)->toBe('v0.0.0');
});

it('can get version tag', function () {
  $version = Versioning::tag();

  expect($version)->toBeString();
});

it('can get full version', function () {
  $version = Versioning::full();

  expect($version)->toBeString();
});

it('can get commit hash', function () {
  $version = Versioning::commit();

  expect($version)->toBeString();
});

it('can get tag with commit', function () {
  $version = Versioning::tagWithCommit();

  expect($version)->toBeString();
});

it('caches version when cache is enabled', function () {
  Config::set('versioning.cache.enabled', true);
  Config::set('versioning.cache.ttl', 3600);

  Cache::shouldReceive('has')
    ->with('app_version_tag')
    ->once()
    ->andReturn(false);

  Cache::shouldReceive('put')
    ->with('app_version_tag', \Mockery::type('string'), 3600)
    ->once();

  Cache::shouldReceive('get')
    ->never();

  Versioning::tag();
});

it('returns cached version when available', function () {
  Config::set('versioning.cache.enabled', true);

  Cache::shouldReceive('has')
    ->with('app_version_tag')
    ->once()
    ->andReturn(true);

  Cache::shouldReceive('get')
    ->with('app_version_tag')
    ->once()
    ->andReturn('v1.0.0');

  $version = Versioning::tag();

  expect($version)->toBe('v1.0.0');
});

it('can clear version cache', function () {
  Config::set('versioning.cache.key', 'app_version');

  Cache::shouldReceive('forget')
    ->with('app_version_tag')
    ->once();

  Cache::shouldReceive('forget')
    ->with('app_version_full')
    ->once();

  Cache::shouldReceive('forget')
    ->with('app_version_commit')
    ->once();

  Cache::shouldReceive('forget')
    ->with('app_version_tag-commit')
    ->once();

  Versioning::clearCache();
});

it('removes version prefix when configured', function () {
  Config::set('versioning.include_prefix', false);
  Config::set('versioning.repository_path', base_path());

  $version = Versioning::tag();

  // If version starts with a number, prefix was removed
  expect($version)->toMatch('/^(\d|dev)/');
});
