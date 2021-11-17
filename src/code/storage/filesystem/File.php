<?php

namespace code\storage\filesystem;

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
        if(is_null($this->fileHandle)){
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
    public function seek(int $offset) {
        return fseek($this->fileHandle, $offset);
    }

}
