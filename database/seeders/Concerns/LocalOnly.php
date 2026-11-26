<?php

namespace Database\Seeders\Concerns;

use RuntimeException;

/** Seeders with invented data refuse to run anywhere but locally and in tests. */
trait LocalOnly
{
    protected function ensureLocal(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('The seeders make invented test data. They run only locally and in tests, never in production or on staging.');
        }
    }
}
