<?php

namespace code\storage\filesystem;

interface StorageItem {

    public function open($mode = null);

    public function close();

    public function stream();

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
    public function filesize(): FileAttributes;

    /**
     * @throws UnableToMoveFile
     * @throws FilesystemException
     */
    public function move(string $destination): bool;

    /**
     * @throws UnableToCopyFile
     * @throws FilesystemException
     */
    public function copy(string $destination): bool;
}
