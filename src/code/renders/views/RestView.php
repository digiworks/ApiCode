<?php

namespace code\renders\views;

use code\rest\RestClient;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class RestView extends View {

    protected $restClient;
    protected $url;
    protected $options = [];

    /**
     * 
     * @param string $url
     * @param string $base_url
     */
    public function __construct(ServerRequestInterface $request, string $url, string $base_url = "", $options = []) {
        $this->url = $url;
        $this->options = $options;
        $this->restClient = new RestClient($base_url);
        $this->restClient->withRequestCookieParams($request);
    }

    /**
     * 
     * @return string
     */
    public function load() {
        $view = "";
        try {
            $view = $this->restClient->get($this->url, ['verify' => false]);
        } catch (Exception $ex) {
            
        }
        return $view;
    }

}
