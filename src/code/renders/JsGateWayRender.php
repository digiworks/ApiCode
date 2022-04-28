<?php

namespace code\renders;

use code\renders\views\RestView;

class JsGateWayRender extends JsRender {

    /** url gateway */
    private $gateway;

    /** */
    public $timeout = 30;

    /** */
    public $restOptions = [];

    public function getGateway() {
        return $this->gateway;
    }

    public function setGateway($gateway): void {
        $this->gateway = $gateway;
    }

    /**
     * 
     * @param string $view
     * @return string
     */
    public function renderView(string $view): string {

        $this->view = new RestView($this->controller->getRequest(), $this->controller->getPath(), $this->gateway);
        return $this->render();
    }

    protected function init($conf) {
        parent::init($conf);
        if (isset($conf['gateway'])) {
            $this->setGateway($conf['gateway']);
        }
    }

}
