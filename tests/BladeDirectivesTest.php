<?php

use Illuminate\Support\Facades\Blade;

it('registers app_version blade directive', function () {
    $directives = Blade::getCustomDirectives();

    expect($directives)->toHaveKey('app_version');
});

it('registers app_version_tag blade directive', function () {
    $directives = Blade::getCustomDirectives();

    expect($directives)->toHaveKey('app_version_tag');
});

it('registers app_version_full blade directive', function () {
    $directives = Blade::getCustomDirectives();

    expect($directives)->toHaveKey('app_version_full');
});

it('registers app_version_commit blade directive', function () {
    $directives = Blade::getCustomDirectives();

    expect($directives)->toHaveKey('app_version_commit');
});

it('app_version_tag directive outputs correct php code', function () {
    $directive = Blade::compileString('@app_version_tag');

    expect($directive)->toContain('Williamug\Versioning\Versioning::tag()');
});

it('app_version_full directive outputs correct php code', function () {
    $directive = Blade::compileString('@app_version_full');

    expect($directive)->toContain('Williamug\Versioning\Versioning::full()');
});

it('app_version_commit directive outputs correct php code', function () {
    $directive = Blade::compileString('@app_version_commit');

    expect($directive)->toContain('Williamug\Versioning\Versioning::commit()');
});
