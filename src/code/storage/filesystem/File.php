<?php

namespace code\storage\filesystem;

use code\exceptions\UnableToCreateDirectory;
use code\exceptions\UnableToRetrieveMetadata;
use DirectoryIterator;
use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Slim\Psr7\Stream;

class File {

    const MODE_APPEND = "a";
    const MODE_WRITE = "wb";
    const MODE_READ = "rb";

    private $path;
    private $fileHandle;
    private $mode = 'rb';

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

    public function __construct($path) {
        $this->path = $path;
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

    public function mime_content_type() {
        return mime_content_type($this->path);
    }

    public function filesize() {
        return filesize($this->path);
    }

    public function delete() {
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
    public function write(string $output) {
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

    public function filemtime() {
        return filemtime($this->path);
    }

    public function move(string $destination) {
        
    }

    public function copy($destination) {
        
    }

    /**
     * 
     * @param string $directory
     * @param int $permissions
     * @param bool $recursive
     * @return type
     */
    public function createDirectory(string $directory,
            int $permissions = 0777,
            bool $recursive = false) {

        return mkdir($this->basePath . DIRECTORY_SEPARATOR . $directory, $permissions, $recursive);
    }

    /**
     * 
     * @param string $directory
     * @return type
     */
    public function deleteDirectory(string $directory) {
        return rmdir($this->basePath . DIRECTORY_SEPARATOR . $directory);
    }

    /**
     * 
     * @param string $path
     * @param int $mode
     * @return Generator
     */
    private function listDirectoryRecursively(
            string $path,
            int $mode = RecursiveIteratorIterator::SELF_FIRST
    ): Generator {
        yield from new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
                        $mode
        );
    }

    /**
     * 
     * @param string $location
     * @return Generator
     */
    private function listDirectory(string $location): Generator {
        $iterator = new DirectoryIterator($location);

        foreach ($iterator as $item) {
            if ($item->isDot()) {
                continue;
            }

            yield $item;
        }
    }

    /**
     * 
     * @param string $dirname
     * @param int $visibility
     * @return void
     * @throws type
     */
    protected function ensureDirectoryExists(string $dirname, int $visibility): void {
        if (is_dir($dirname)) {
            return;
        }

        error_clear_last();

        if (!@mkdir($dirname, $visibility, true)) {
            $mkdirError = error_get_last();
        }

        clearstatcache(true, $dirname);

        if (!is_dir($dirname)) {
            $errorMessage = isset($mkdirError['message']) ? $mkdirError['message'] : '';

            throw UnableToCreateDirectory::atLocation($dirname, $errorMessage);
        }
    }

    /**
     * 
     * @param string $directory
     * @return type
     */
    public function diskfreespace($directory) {
        return disk_free_space($directory);
    }

    /**
     * 
     * @param string $directory
     * @return type
     */
    public function disktotalspace($directory) {
        return disk_total_space($directory);
    }

    /**
     * 
     * @param string $url
     * @return type
     */
    public static function fileExists($url) {
        return is_file($url);
    }

    /**
     * 
     * @param string $url
     * @return type
     */
    public function dirname($url) {
        return dirname($url);
    }

    /**
     * 
     * @param string $url
     * @return type
     */
    public function realpath($url) {
        return realpath($url);
    }

    /**
     * 
     * @param string $path
     * @return FileAttributes
     * @throws type
     */
    public function mimeType(string $path): FileAttributes {
        error_clear_last();
        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromFile($path);

        if ($mimeType === null) {
            throw UnableToRetrieveMetadata::mimeType($path, error_get_last()['message'] ?? '');
        }

        return new FileAttributes($path, null, null, null, $mimeType);
    }

    /**
     * 
     * @param string $path
     * @return FileAttributes
     * @throws type
     */
    public function lastModified(string $path): FileAttributes {
        error_clear_last();
        $lastModified = @filemtime($path);

        if ($lastModified === false) {
            throw UnableToRetrieveMetadata::lastModified($path, error_get_last()['message'] ?? '');
        }

        return new FileAttributes($path, null, null, $lastModified);
    }

    /**
     * 
     * @param string $path
     * @return FileAttributes
     * @throws type
     */
    public function fileSize(string $path): FileAttributes {
        error_clear_last();

        if (is_file($location) && ($fileSize = @filesize($path)) !== false) {
            return new FileAttributes($path, $fileSize);
        }

        throw UnableToRetrieveMetadata::fileSize($path, error_get_last()['message'] ?? '');
    }

}
