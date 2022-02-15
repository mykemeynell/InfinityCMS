<?php

namespace Infinity;

class Seed
{
    public static function getFolderName(): string
    {
        return version_compare(app()->version(), '8.0') >= 0 ? 'seeders' : 'seeds';
    }
}
