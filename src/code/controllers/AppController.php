<?php

namespace code\controllers;

use code\applications\ApiAppFactory;
use code\components\Component;
use code\debugger\Debugger;
use code\service\ServiceTypes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Routing\RouteContext;

abstract class AppController {

    /** @var ServerRequestInterface $request */
    protected $request;

    /** @var ResponseInterface $request */
    protected $response;
    protected $currentView;
    protected $theme;

    /** @var Component $component */
    protected $component;

    /** @var ResponseBuilder $responsebuilder */
    protected $responsebuilder = null;

    public function getRequest() {
        return $this->request;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getCurrentView() {
        return $this->currentView;
    }

    public function getTheme() {
        return $this->theme;
    }

    public function getResponsebuilder(): ResponseBuilder {
        return $this->responsebuilder;
    }

    public function setRequest($request): AppController {
        $this->request = $request;
        return $this;
    }

    public function setResponse($response): AppController {
        $this->response = $response;
        $this->responsebuilder = new ResponseBuilder($this->response);
        return $this;
    }

    /**
     * 
     * @param string $currentView
     * @return AppController
     */
    public function setCurrentView($currentView): AppController {
        $this->currentView = $currentView;
        return $this;
    }

    public function useTheme($theme): AppController {
        $this->theme = $theme;
        return $this;
    }

    /**
     * 
     * @return Component
     */
    public function getComponent(): Component {
        return $this->component;
    }

    /**
     * 
     * @param Component $component
     * @return AppController
     */
    public function setComponent($component): AppController {
        $this->component = $component;
        return $this;
    }

    public function __construct() {
        $this->init();
    }

    public function init() {
        
    }

    /**
     * 
     */
    public function render() {
        $renderManager = ApiAppFactory::getApp()->getService(ServiceTypes::RENDER);
        $render = $renderManager->getRender($this);
        if (!is_null($this->theme)) {
            $render->useTheme($this->theme);
        }
        if (!is_null($this->component)) {
            $render->addStylesheets($this->component->loadStylesheets());
            $render->addImports($this->component->loadImports());
        }
        $this->response->getBody()->write($render->renderView($this->getFullViewPath($this->currentView)));
        return $this;
    }

    /**
     * 
     * @param string $view
     * @return string
     */
    protected function getFullViewPath($view) {
        $path = "";
        if (!is_null($this->component)) {
            $path = $this->component->calculatePath($view);
        } else {
            $path = $view;
        }
        return $path;
    }

    /**
     * 
     * @param string $status
     * @param array $data
     * @param string $message
     * @return ResponseInterface
     */
    public function buildRestResponse($status = "succesful", $data = [], $message = null) {
        $send_to_response = [
            "status" => $status,
            "data" => $data,
            "message" => $message
        ];
        $config = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS);
        $debug = $config->get("env.debug", false);
        if ($debug) {
            Debugger::processAjaxBuffer();
            $send_to_response['debugger'] ['buffer'] = Debugger::getBuffer();
            $send_to_response['debugger'] ['trace'] = Debugger::getTrace();
            $send_to_response['debugger'] ['Coverage'] = Debugger::getCoverage();
        }
        $this->response->getBody()->write(json_encode($send_to_response));
        return $this;
    }

    public function buildViewResponse() {
        $this->response = $this->getResponsebuilder()->buildViewResponse();
        return $this;
    }

    public function getBaseUrl() {
        /** @var UriInterface $uri */
        $uri = $this->getRequest()->getUri();
        $basePath = $uri->getHost();
        return $basePath;
    }

    public function getPath() {
        /** @var UriInterface $uri */
        $uri = $this->getRequest()->getUri();
        $path = $uri->getPath();
        return $path;
    }

    public function getFullUrl() {
        /** @var UriInterface $uri */
        $uri = $this->getRequest()->getUri();
        $scheme = $uri->getScheme();
        $basePath = $uri->getHost();

        return ($scheme !== '' ? $scheme . ':' : '')
                . ($basePath ? '//' . $basePath : '');
    }

    /**
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public function renderView(ServerRequestInterface $request, ResponseInterface $response, array $args) {

        try {
            $routeContext = RouteContext::fromRequest($request);
            $route = $routeContext->getRoute();
            $name = $route->getName();
            $groups = $route->getGroups();
            $methods = $route->getMethods();
            $arguments = $route->getArguments();
            $basePath = $routeContext->getBasePath();

            $this->setRequest($request)->setResponse($response);
        } catch (Exception $ex) {
            ApiAppFactory::getApp()->getLogger()->error("error", $ex->getMessage());
            ApiAppFactory::getApp()->getLogger()->error("error", $ex->getTraceAsString());
        }
        return $this->getResponse();
    }

}
