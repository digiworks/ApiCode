<?php

namespace code\uri;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * The UriFactory class.
 */
class UriFactory implements UriFactoryInterface {

    /**
     * @inheritDoc
     */
    public static function createUri(string $uri = ''): UriInterface {
        return new Uri($uri);
    }

}
