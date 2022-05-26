<?php

namespace code\renders;

use code\renders\views\RestView;
use code\uri\UriFactory;
use code\utility\Arr;

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

    
    public function __construct($conf) {
        parent::__construct($conf);
        $this->remoteRender = true;
    }

    
    /**
     * 
     * @param string $view
     * @return string
     */
    public function renderView(string $view): string {

        $this->view = new RestView($this->controller->getRequest(), $view, $this,  $this->gateway);
        return $this->render();
    }

    protected function init($conf) {
        if (isset($conf['gateway'])) {
            $this->setGateway($conf['gateway']);
        }
        parent::init($conf);
    }
}
