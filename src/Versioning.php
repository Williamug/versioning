<?php

namespace Williamug\Versioning;

class Versioning
{
    public static function tag(): string
    {
        return exec('git describe --tags --abbrev=0');
    }
}
