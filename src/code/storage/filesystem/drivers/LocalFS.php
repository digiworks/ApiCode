<?php

namespace code\storage\filesystem\drivers;

use code\exceptions\SymbolicLinkEncountered;
use code\exceptions\UnableToCreateDirectory;
use code\storage\filesystem\DirectoryAttributes;
use code\storage\filesystem\File;
use code\storage\filesystem\FileAttributes;
use code\storage\filesystem\FileSystem;
use code\storage\filesystem\StorageDriverInterface;
use DirectoryIterator;
use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class LocalFS implements StorageDriverInterface {

    const SKIP_LINKS = 0001;
    const DISALLOW_LINKS = 0002;

    /**
     * @var int
     */
    private $linkHandling;
    private $filesystem;

    public function __construct(FileSystem $filesystem, int $linkHandling = self::DISALLOW_LINKS) {
        $this->linkHandling = $linkHandling;
        $this->filesystem = $filesystem;
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
            bool $recursive = false): bool {

        return mkdir($this->filesystem->getBaseRootPath() . DIRECTORY_SEPARATOR . $directory, $permissions, $recursive);
    }

    /**
     * 
     * @param string $directory
     * @return type
     */
    public function deleteDirectory(string $directory): bool {
        return rmdir($this->filesystem->getBaseRootPath() . $directory);
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

    public function listContents(string $path, bool $deep): iterable {
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
        return disk_free_space($this->filesystem->getBaseRootPath() . $this->filesystem->normalizePath($directory));
    }

    /**
     * 
     * @param string $directory
     * @return type
     */
    public function disktotalspace($directory) {
        return disk_total_space($this->filesystem->getBaseRootPath() . $this->filesystem->normalizePath($directory));
    }

    /**
     * 
     * @param string $url
     * @return type
     */
    public function fileExists(string $path): bool {
        return is_file($this->filesystem->getBaseRootPath() . $this->filesystem->normalizePath($path));
    }

    /**
     * 
     * @param string $url
     * @return type
     */
    public function dirname($url) {
        return str_replace($this->filesystem->getBaseRootPath(), '', dirname($this->filesystem->normalizePath($url)));
    }

    /**
     * 
     * @param string $url
     * @return type
     */
    public function realpath($url) {
        return realpath($this->filesystem->getBaseRootPath() . $this->filesystem->normalizePath($url));
    }

    /**
     * 
     * @param string $path
     * @return File
     */
    public function createStorageItem(string $path) {
        return new File($path);
    }

    /**
     * 
     * @param string $url
     */
    public function createAbsolutePath(string $url) {
        return $this->filesystem->getBaseRootPath()  . $this->filesystem->normalizePath($url);
    }

}
