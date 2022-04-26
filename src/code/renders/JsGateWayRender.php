<?php

namespace code\renders;

use code\renders\views\RestView;

class JsGateWayRender extends JsRender {

    public $Gateway;
    public $timeout = 30;
    public $restOptions = [];
    
    /**
     * 
     * @param string $view
     * @return string
     */
    public function renderView(string $view): string {
        $this->view = new RestView($view);
        return $this->render();
    }

}
