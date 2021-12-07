<?php

namespace code\storage\filesystem\drivers;

use code\exceptions\SymbolicLinkEncountered;
use code\exceptions\UnableToCreateDirectory;
use code\storage\filesystem\DirectoryAttributes;
use code\storage\filesystem\File;
use code\storage\filesystem\FileAttributes;
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

    public function __construct(int $linkHandling = self::DISALLOW_LINKS) {
        $this->linkHandling = $linkHandling;
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
            bool $recursive = false): bool {

        return mkdir($this->basePath . DIRECTORY_SEPARATOR . $directory, $permissions, $recursive);
    }

    /**
     * 
     * @param string $directory
     * @return type
     */
    public static function deleteDirectory(string $directory): bool {
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
    public static function diskfreespace($directory) {
        return disk_free_space($directory);
    }

    /**
     * 
     * @param string $directory
     * @return type
     */
    public static function disktotalspace($directory) {
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
    public static function dirname($url) {
        return dirname($url);
    }

    /**
     * 
     * @param string $url
     * @return type
     */
    public static function realpath($url) {
        return realpath($url);
    }

    public function createStorageItem($path) {
        return new File($path);
    }

}
