<?php

namespace code\utility\assert;

use RuntimeException;

/**
 * The RuntimeAssert class.
 */
class RuntimeAssert extends TypeAssert
{
    protected static function exception(): callable
    {
        return static fn(string $message) => new RuntimeException($message);
    }
}