<?php

namespace Williamug\Versioning;

class Versioning
{
    public function tag(): string
    {
        return exec('git describe --tags --abbrev=0');
    }
}
