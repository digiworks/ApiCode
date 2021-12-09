<?php

namespace code\controllers;

use code\applications\ApiAppFactory;
use code\components\Component;
use code\debugger\Debugger;
use code\service\ServiceTypes;
use Psr\Http\Message\ResponseInterface;

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

    public function setRequest($request): AppController {
        $this->request = $request;
        return $this;
    }

    public function setResponse($response): AppController {
        $this->response = $response;
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

    /**
     * 
     */
    public function render() {
        $renderManager = ApiAppFactory::getApp()->getService(ServiceTypes::RENDER);
        $render = $renderManager->getRender();
        if (!is_null($this->theme)) {
            $render->useTheme($this->theme);
        }
        $this->response->getBody()->write($render->renderView($this->getFullViewPath($this->currentView)));
    }

    /**
     * 
     * @param string $view
     * @return string
     */
    protected function getFullViewPath($view) {
        $path = "";
        if (!is_null($this->component)) {
            $path = $this->component->getBasePath() . DIRECTORY_SEPARATOR . $view;
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
        return $this->response;
    }

}
