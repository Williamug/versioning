<?php

if (!function_exists('app_version')) {
    function app_version()
    {
        return exec('git describe --tags --abbrev=0');
    }
}
