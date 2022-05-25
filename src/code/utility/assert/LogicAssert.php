<?php



namespace code\utility\assert;

use LogicException;


/**
 * The LogicAssert class.
 */
class LogicAssert extends TypeAssert
{
    protected static function exception(): callable
    {
        return static fn(string $message) => new LogicException($message);
    }
}
