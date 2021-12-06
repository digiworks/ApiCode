<?php

namespace code\storage\filesystem\mimetypes;

interface MimeTypeDetector {

    /**
     * @param string|resource $contents
     */
    public function detectMimeType(string $path, $contents): ?string;

    public function detectMimeTypeFromBuffer(string $contents): ?string;

    public function detectMimeTypeFromPath(string $path): ?string;

    public function detectMimeTypeFromFile(string $path): ?string;
}
