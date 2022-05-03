<?php

namespace code\storage\filesystem\drivers;

use code\storage\filesystem\AwsS3File;
use code\storage\filesystem\FileSystem;
use code\storage\filesystem\StorageDriverInterface;
use Throwable;

class AwsS3V3 implements StorageDriverInterface {

    /**
     * @var string[]
     */
    public const AVAILABLE_OPTIONS = [
        'ACL',
        'CacheControl',
        'ContentDisposition',
        'ContentEncoding',
        'ContentLength',
        'ContentType',
        'Expires',
        'GrantFullControl',
        'GrantRead',
        'GrantReadACP',
        'GrantWriteACP',
        'Metadata',
        'MetadataDirective',
        'RequestPayer',
        'SSECustomerAlgorithm',
        'SSECustomerKey',
        'SSECustomerKeyMD5',
        'SSEKMSKeyId',
        'ServerSideEncryption',
        'StorageClass',
        'Tagging',
        'WebsiteRedirectLocation',
    ];

    /**
     * @var string[]
     */
    private const EXTRA_METADATA_FIELDS = [
        'Metadata',
        'StorageClass',
        'ETag',
        'VersionId',
    ];

    /**
     * @var S3ClientInterface
     */
    private $client;

    public function __construct(FileSystem $filesystem,
            S3ClientInterface $client = null
    ) {
        $this->client = $client;
    }

    public function fileExists(string $path): bool {
        try {
            return $this->client->doesObjectExist($this->bucket, $this->prefixer->prefixPath($path), $this->options);
        } catch (Throwable $exception) {
            throw UnableToCheckFileExistence::forLocation($path, $exception);
        }
    }

    public function createStorageItem($path) {
        new AwsS3File($path, $this->client);
    }

    public static function createDirectory(string $directory, int $permissions = 0777, bool $recursive = false): bool {
        
    }

    public function deleteDirectory(string $path): bool {
        
    }

    public function dirname($url) {
        
    }

    public function diskfreespace($directory) {
        
    }

    public function disktotalspace($directory) {
        
    }

    public function listContents(string $path, bool $deep): iterable {
        
    }

    public function realpath($url) {
        
    }

}
