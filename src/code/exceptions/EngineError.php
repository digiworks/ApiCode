<?php
namespace code\exceptions;

use RuntimeException;

class EngineError extends RuntimeException
{
    /** @var \Exception */
    protected $originalException;

    public static function withException($exception): self
    {
        $error = new self();

        $error->originalException = $exception;

        return $error;
    }

    public function getException(): Exception
    {
        return $this->originalException ?? $this;
    }
}
