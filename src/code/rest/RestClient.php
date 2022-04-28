<?php

namespace code\rest;

use GuzzleHttp\Client;

class RestClient {

    protected $client;

    public function __construct($base_uri, $timeout = 10) {
        $config = [
            // Base URI is used with relative requests
            'base_uri' => $base_uri,
            // You can set any number of default request options.
            'timeout' => $timeout,
        ];
        $this->client = new Client($config);
    }

    public function get($url, $params = []) {
        return $this->client->get($url, $params)->getBody()->getContents();
    }

}
