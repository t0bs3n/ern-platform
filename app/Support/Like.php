<?php

namespace App\Support;

/**
 * A LIKE pattern for "contains", with LIKE's own special characters made harmless.
 * Use it with "column LIKE ? ESCAPE '!'". The escape character is "!", because
 * SQLite and MySQL disagree about the backslash.
 */
final class Like
{
    public static function contains(string $value): string
    {
        return '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value).'%';
    }
}
