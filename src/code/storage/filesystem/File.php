<?php

namespace code\storage\filesystem;

use code\exceptions\UnableToRetrieveMetadata;
use code\storage\filesystem\mimetypes\FinfoMimeTypeDetector;
use code\storage\filesystem\mimetypes\MimeTypeDetector;
use Slim\Psr7\Stream;

class File extends StorageItem {

    const MODE_APPEND = "a";
    const MODE_WRITE = "wb";
    const MODE_READ = "rb";

    private $fileHandle;
    private $mode = 'rb';

    /**
     * @var MimeTypeDetector
     */
    private $mimeTypeDetector;

    public function getPath() {
        return $this->path;
    }

    public function getFileHandle() {
        return $this->fileHandle;
    }

    public function getMode() {
        return $this->mode;
    }

    public function setPath($path): void {
        $this->path = $path;
    }

    public function setFileHandle($fileHandle): void {
        $this->fileHandle = $fileHandle;
    }

    public function setMode($mode): void {
        $this->mode = $mode;
    }

    public function __construct($path, MimeTypeDetector $mimeTypeDetector = null, array $options = []) {
        $this->path = $path;
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector();
    }

    public function open($mode = null) {
        if (!is_null($mode)) {
            $this->mode = $mode;
        }
        $this->fileHandle = fopen($this->path, $this->mode);
    }

    public function close() {
        fclose($this->fileHandle);
        $this->fileHandle = null;
    }

    public function basename() {
        return basename($this->path);
    }

    /**
     * 
     * @return string
     */
    public function mime_content_type() {
        return $this->mimeType()->mimeType();
    }

    public function delete(): void {
        if (!is_null($this->fileHandle)) {
            $this->close();
        }
        unlink($this->path);
    }

    public function read() {
        return fread($this->fileHandle);
    }

    public function stream() {
        if (is_null($this->fileHandle)) {
            $this->open();
        }
        return new Stream($this->fileHandle);
    }

    /**
     * 
     * @param string $output
     * @return int|false
     */
    public function write(string $output): int {
        return fwrite($this->fileHandle, $output, strlen($output));
    }

    /**
     * 
     * @param int $offset
     * @return int
     */
    public function seek(int $offset, int $whence = SEEK_SET) {
        return fseek($this->fileHandle, $offset, $whence);
    }

    /**
     * 
     * @return type
     */
    public function filemtime() {
        return filemtime($this->path);
    }

    public function move(string $destination): bool {
        
    }

    public function copy($destination): bool {
        
    }

    /**
     * 
     * @return FileAttributes
     * @throws type
     */
    public function mimeType(): FileAttributes {
        error_clear_last();
        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromFile($this->path);

        if ($mimeType === null) {
            throw UnableToRetrieveMetadata::mimeType($this->path, error_get_last()['message'] ?? '');
        }

        return new FileAttributes($this->path, null, null, $mimeType);
    }

    /**
     * 
     * @return FileAttributes
     * @throws type
     */
    public function lastModified(): FileAttributes {
        error_clear_last();
        $lastModified = @filemtime($this->path);

        if ($lastModified === false) {
            throw UnableToRetrieveMetadata::lastModified($this->path, error_get_last()['message'] ?? '');
        }

        return new FileAttributes($this->path, null, null, $lastModified);
    }

    /**
     * 
     * @return FileAttributes
     * @throws type
     */
    public function filesize(): FileAttributes {
        $fileSize = 0;
        error_clear_last();

        if (is_file($this->path) && ($fileSize = @filesize($this->path)) !== false) {
            return new FileAttributes($this->path, $fileSize);
        }

        throw UnableToRetrieveMetadata::fileSize($this->path, error_get_last()['message'] ?? '');
    }

    /**
     * 
     */
    public function file_get_contents(): string {
        $buffer = "";
        try {
            $stream = $this->stream();
            $buffer = $stream->read($this->filesize());
            $stream->close();
        } catch (Exception $ex) {
            
        }
        return $buffer;
    }

}
