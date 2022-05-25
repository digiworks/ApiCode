<?php

namespace code\renders\views;

use code\rest\RestClient;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class RestView extends View {

    protected $restClient;
    protected $url;
    protected $options = [];
    protected $render;

    /**
     * 
     * @param string $url
     * @param string $base_url
     */
    public function __construct(ServerRequestInterface $request, string $url, $render, string $base_url = "", $options = []) {
        $this->url = $url;
        $this->options = $options;
        $this->restClient = new RestClient($base_url);
        $this->restClient->withRequestCookieParams($request);
        $this->render = $render;
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
            $this->render->loadImports($restRender['imports']);
            $this->render->loadStylesheets($restRender['stylesheets']);
            $view = $restRender['view'];
        } catch (Exception $ex) {
            ApiAppFactory::getApp()->getLogger()->error("error", $ex->getMessage());
            ApiAppFactory::getApp()->getLogger()->error("error", $ex->getTraceAsString());
        }
        return $view;
    }

}
