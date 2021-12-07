<?php

namespace code\storage\filesystem;

use code\exceptions\FilesystemException;
use code\exceptions\UnableToCreateDirectory;
use code\exceptions\UnableToDeleteDirectory;
use code\exceptions\UnableToDeleteFile;
use code\exceptions\UnableToMoveFile;
use code\exceptions\UnableToRetrieveMetadata;

interface StorageDriver {

    /**
     * @throws FilesystemException
     */
    public static function fileExists(string $path): bool;

    /**
     * @throws UnableToDeleteDirectory
     * @throws FilesystemException
     */
    public static function deleteDirectory(string $path): bool;

    /**
     * @throws UnableToCreateDirectory
     * @throws FilesystemException
     */
    public static function createDirectory(string $directory,
            int $permissions = 0777,
            bool $recursive = false): bool;

    /**
     * @return iterable<StorageAttributes>
     *
     * @throws FilesystemException
     */
    public static function listContents(string $path, bool $deep): iterable;

    public static function diskfreespace($directory);

    public static function disktotalspace($directory);

    public static function dirname($url);

    public static function realpath($url);

    public function createStorageItem($path);
}
