<?php

namespace code\controllers;

use code\applications\ApiAppFactory;
use code\components\Component;
use code\service\ServiceTypes;

abstract class AppController {

    protected $request;
    protected $response;

    /** @var Component $component */
    protected $component;

    public function getComponent() {
        return $this->component;
    }

    public function setComponent($component): void {
        $this->component = $component;
    }

    public function render($currentView, $theme = null) {
        $renderManager = ApiAppFactory::getApp()->getService(ServiceTypes::RENDER);
        $rennder = $renderManager->getRender();
        if (!is_null($theme)) {
            $rennder->useTheme($theme);
        }
        $this->response->getBody()->write($rennder->renderView($this->getFullViewPath($currentView)));
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
