<?php

namespace code\exceptions;

use code\exceptions\FilesystemOperationFailed;
use RuntimeException;
use Throwable;

class UnableToMoveFile extends RuntimeException implements FilesystemOperationFailed {

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $destination;

    public function source(): string {
        return $this->source;
    }

    public function destination(): string {
        return $this->destination;
    }

    public static function fromLocationTo(
            string $sourcePath,
            string $destinationPath,
            Throwable $previous = null
    ): UnableToMoveFile {
        $e = new static("Unable to move file from $sourcePath to $destinationPath", 0, $previous);
        $e->source = $sourcePath;
        $e->destination = $destinationPath;

        return $e;
    }

    public function operation(): string {
        return FilesystemOperationFailed::OPERATION_MOVE;
    }

}
