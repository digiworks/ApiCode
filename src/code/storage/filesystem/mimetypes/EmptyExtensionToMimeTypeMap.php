<?php

namespace code\storage\filesystem\mimetypes;

class EmptyExtensionToMimeTypeMap implements ExtensionToMimeTypeMap {

    public function lookupMimeType(string $extension): ?string {
        return null;
    }

}
