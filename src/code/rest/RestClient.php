<?php

namespace code\rest;

use GuzzleHttp\Client;

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
        if ($request->getHeader('Cookie')) {
            $this->cookie = $request->getHeader('Cookie');
        }
    }

    public function get($url, $params = []) {
        if (!is_null($this->cookie)) {
            $params['headers'] = ['Cookie' => $this->cookie];
        }
        return $this->client->get($url, $params)->getBody()->getContents();
    }

}
