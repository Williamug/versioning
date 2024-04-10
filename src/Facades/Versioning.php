<?php

namespace Williamug\Versioning\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Williamug\Versioning\Versioning
 */
class Versioning extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Williamug\Versioning\Versioning::class;
    }
}
