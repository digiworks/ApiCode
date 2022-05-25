<?php

namespace code\renders\views;

use code\rest\RestClient;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class RestView extends View {

    protected $restClient;
    protected $url;
    protected $options = [];
    protected $imports = [];
    protected $stylesheets = [];

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
            $response = $this->restClient->get($this->url, ['verify' => false]);
            $restRender = json_decode($response, true);
            $this->loadImports($restRender);
            $this->loadStylesheets($restRender);
            $view = $restRender['view'];
        } catch (Exception $ex) {
            ApiAppFactory::getApp()->getLogger()->error("error", $ex->getMessage());
            ApiAppFactory::getApp()->getLogger()->error("error", $ex->getTraceAsString());
        }
        return $view;
    }

    protected function loadImports($restRender) {
        if (isset($restRender['imports'])) {
            $this->imports = $restRender['imports'];
        }
    }

    protected function loadStylesheets($restRender) {
        if (isset($restRender['stylesheets'])) {
            $this->stylesheets = $restRender['stylesheets'];
        }
    }

    public function getImports() {
        return $this->imports;
    }

    public function getStylesheets() {
        return $this->stylesheets;
    }

}
