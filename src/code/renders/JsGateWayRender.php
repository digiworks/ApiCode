<?php

namespace code\renders;

use code\renders\views\RestView;

class JsGateWayRender extends JsRender {

    /** url gateway*/
    public $gateway;
    /** */
    public $timeout = 30;
    /** */
    public $restOptions = [];
    
    /**
     * 
     * @param string $view
     * @return string
     */
    public function renderView(string $view): string {
        
        $this->view = new RestView($this->controller->getRequest(), $this->controller->getPath(), $this->gateway);
        return $this->render();
    }

}
