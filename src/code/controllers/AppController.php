<?php

namespace code\controllers;

use code\applications\ApiAppFactory;
use code\components\Component;
use code\service\ServiceTypes;

abstract class AppController {

    protected $request;
    protected $response;
    protected $currentView;
    protected $theme;

    /** @var Component $component */
    protected $component;

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

    public function setRequest($request) {
        $this->request = $request;
        return $this;
    }

    public function setResponse($response) {
        $this->response = $response;
        return $this;
    }

    public function setCurrentView($currentView) {
        $this->currentView = $currentView;
        return $this;
    }

    public function useTheme($theme) {
        $this->theme = $theme;
        return $this;
    }

    public function getComponent() {
        return $this->component;
    }

    public function setComponent($component): void {
        $this->component = $component;
    }

    public function render() {
        $renderManager = ApiAppFactory::getApp()->getService(ServiceTypes::RENDER);
        $render = $renderManager->getRender();
        if (!is_null($this->theme)) {
            $render->useTheme($this->theme);
        }
        $this->response->getBody()->write($render->renderView($this->getFullViewPath($this->currentView)));
    }

    protected function getFullViewPath($view) {
        $path = "";
        if (!is_null($this->component)) {
            $path = $this->component->getBasePath() . DIRECTORY_SEPARATOR . $view;
        } else {
            $path = $view;
        }
        return $path;
    }

}
