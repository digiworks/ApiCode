<?php

namespace code\storage\filesystem;

use code\exceptions\SymbolicLinkEncountered;
use code\exceptions\UnableToCreateDirectory;
use code\exceptions\UnableToRetrieveMetadata;
use code\storage\filesystem\mimetypes\FinfoMimeTypeDetector;
use code\storage\filesystem\mimetypes\MimeTypeDetector;
use DirectoryIterator;
use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Slim\Psr7\Stream;
use SplFileInfo;

class File implements StorageDriver {

    const SKIP_LINKS = 0001;
    const DISALLOW_LINKS = 0002;
    const MODE_APPEND = "a";
    const MODE_WRITE = "wb";
    const MODE_READ = "rb";

    private $path;
    private $fileHandle;
    private $mode = 'rb';

    /**
     * @var MimeTypeDetector
     */
    private $mimeTypeDetector;

    /**
     * @var int
     */
    private $linkHandling;

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

    public function __construct($path, MimeTypeDetector $mimeTypeDetector = null, int $linkHandling = self::DISALLOW_LINKS) {
        $this->path = $path;
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector();
        $this->linkHandling = $linkHandling;
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
        return $this->mimeType();
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
    public static function createDirectory(string $directory,
            int $permissions = 0777,
            bool $recursive = false) {

        return mkdir($this->basePath . DIRECTORY_SEPARATOR . $directory, $permissions, $recursive);
    }

    /**
     * 
     * @param string $directory
     * @return type
     */
    public static function deleteDirectory(string $directory) {
        return rmdir($this->basePath . DIRECTORY_SEPARATOR . $directory);
    }

    /**
     * 
     * @param string $path
     * @param int $mode
     * @return Generator
     */
    private static function listDirectoryRecursively(
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
    private static function listDirectory(string $location): Generator {
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
    public static function fileExists(string $path): bool {
        return is_file($path);
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
     * @return FileAttributes
     * @throws type
     */
    public function mimeType(): FileAttributes {
        error_clear_last();
        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromFile($this->path);

        if ($mimeType === null) {
            throw UnableToRetrieveMetadata::mimeType($this->path, error_get_last()['message'] ?? '');
        }

        return new FileAttributes($this->path, null, null, null, $mimeType);
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
    public function fileSize(): FileAttributes {
        error_clear_last();

        if (is_file($location) && ($fileSize = @filesize($this->path)) !== false) {
            return new FileAttributes($this->path, $fileSize);
        }

        throw UnableToRetrieveMetadata::fileSize($this->path, error_get_last()['message'] ?? '');
    }

    public static function listContents(string $path, bool $deep): iterable {
        /** @var SplFileInfo[] $iterator */
        $iterator = $deep ? $this->listDirectoryRecursively($path) : $this->listDirectory($path);

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isLink()) {
                if ($this->linkHandling & self::SKIP_LINKS) {
                    continue;
                }
                throw SymbolicLinkEncountered::atLocation($fileInfo->getPathname());
            }

            $path = $fileInfo->getPathname();
            $lastModified = $fileInfo->getMTime();
            $isDirectory = $fileInfo->isDir();
            $permissions = octdec(substr(sprintf('%o', $fileInfo->getPerms()), -4));

            yield $isDirectory ? new DirectoryAttributes($path, $lastModified) : new FileAttributes(
                                    str_replace('\\', '/', $path),
                                    $fileInfo->getSize(),
                                    $lastModified
            );
        }
    }

}
