<?php

namespace code\storage\filesystem\mimetypes;

interface ExtensionToMimeTypeMap {

    public function lookupMimeType(string $extension): ?string;
}
