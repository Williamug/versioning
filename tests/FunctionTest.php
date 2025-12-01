<?php

it('app_version function exists', function () {
    expect(function_exists('app_version'))->toBeTrue();
});

it('app_version returns string', function () {
    $version = app_version();

    expect($version)->toBeString();
});

it('app_version accepts format parameter', function () {
    $tag = app_version('tag');
    $full = app_version('full');
    $commit = app_version('commit');

    expect($tag)->toBeString();
    expect($full)->toBeString();
    expect($commit)->toBeString();
});

it('app_version returns dev as fallback', function () {
    // Create a temporary directory without git
    $tempDir = sys_get_temp_dir().'/test_no_git_'.uniqid();
    mkdir($tempDir);

    $originalDir = getcwd();
    chdir($tempDir);

    $version = app_version();

    chdir($originalDir);
    rmdir($tempDir);

    expect($version)->toBe('dev');
});

it('app_version handles invalid format gracefully', function () {
    $version = app_version('invalid_format');

    expect($version)->toBeString();
});
