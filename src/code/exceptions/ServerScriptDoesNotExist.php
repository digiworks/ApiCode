<?php

namespace code\exceptions;

use RuntimeException;


class ServerScriptDoesNotExist extends RuntimeException
{
    public static function atPath(string $path): self
    {
        return new self("Server script at path `{$path}` doesn't exist");
    }
}
