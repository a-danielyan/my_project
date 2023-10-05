<?php

namespace App\Helpers;

class DBHelper
{
    public static function escapeForLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
