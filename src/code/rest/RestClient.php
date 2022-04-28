<?php

namespace code\rest;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

class RestClient {

    protected $client;
    protected $cookie;

    public function __construct($base_uri, $timeout = 10) {
        $config = [
            // Base URI is used with relative requests
            'base_uri' => $base_uri,
            // You can set any number of default request options.
            'timeout' => $timeout,
        ];
        $this->client = new Client($config);
    }

    public function addCookieParams($cookies) {
        $cookie = SetCookie::fromString($cookies);
        $$this->cookie = new CookieJar();
        $this->cookie->setCookie($cookie);
    }

    public function get($url, $params = []) {
        if (!is_null($this->cookie)) {
            $params['cookies'] = $this->cookie;
        }
        return $this->client->get($url, $params)->getBody()->getContents();
    }

}
