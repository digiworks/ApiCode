<?php

namespace code\storage\filesystem;

class AwsS3File implements StorageItemInterface {

    public function __construct($path, MimeTypeDetector $mimeTypeDetector = null, array $options = []) {
        $this->path = $path;
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
