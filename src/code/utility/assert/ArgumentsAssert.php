<?php

namespace code\utility\assert;

use InvalidArgumentException;

class ArgumentsAssert extends TypeAssert {

    protected static function exception(): callable {
        return static fn(string $msg) => new InvalidArgumentException($msg);
    }

}
