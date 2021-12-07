<?php

namespace code\storage\filesystem;

use code\storage\filesystem\mimetypes\FinfoMimeTypeDetector;
use code\storage\filesystem\mimetypes\MimeTypeDetector;

class AwsS3File extends StorageItem {

    /**
     * @var S3ClientInterface
     */
    private $client;

    public function __construct($path, $client, MimeTypeDetector $mimeTypeDetector = null, array $options = []) {
        $this->path = $path;
        $this->client = $client;
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector();
    }

    public function close() {
        
    }

    public function copy(string $destination): bool {
        
    }

    public function delete(): void {
        
    }

    public function filesize(): FileAttributes {
        
    }

    public function lastModified(): FileAttributes {
        
    }

    public function mimeType(): FileAttributes {
        
    }

    public function move(string $destination): bool {
        
    }

    public function open($mode = null) {
        
    }

    public function read() {
        
    }

    public function stream() {
        
    }

    public function write(string $output): int {
        
    }

}
