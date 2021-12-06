<?php

namespace code\storage\filesystem;

use code\exceptions\FilesystemException;
use code\exceptions\UnableToCreateDirectory;
use code\exceptions\UnableToDeleteDirectory;
use code\exceptions\UnableToDeleteFile;
use code\exceptions\UnableToMoveFile;
use code\exceptions\UnableToRetrieveMetadata;

interface StorageDriver {

    public function open($mode = null);

    public function close();

    public function stream();

    /**
     * @throws FilesystemException
     */
    public static function fileExists(string $path): bool;

    /**
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function write(string $output): int;

    /**
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function read();

    /**
     * @throws UnableToDeleteFile
     * @throws FilesystemException
     */
    public function delete(): void;

    /**
     * @throws UnableToDeleteDirectory
     * @throws FilesystemException
     */
    public static function deleteDirectory(string $path): void;

    /**
     * @throws UnableToCreateDirectory
     * @throws FilesystemException
     */
    public static function createDirectory(string $directory,
            int $permissions = 0777,
            bool $recursive = false): void;

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function mimeType(): FileAttributes;

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function lastModified(): FileAttributes;

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function fileSize(): FileAttributes;

    /**
     * @return iterable<StorageAttributes>
     *
     * @throws FilesystemException
     */
    public static function listContents(string $path, bool $deep): iterable;

    /**
     * @throws UnableToMoveFile
     * @throws FilesystemException
     */
    public function move(string $destination): void;

    /**
     * @throws UnableToCopyFile
     * @throws FilesystemException
     */
    public function copy(string $destination): void;
}
