<?php

namespace code\renders;

use code\renders\views\RestView;

class JsRestRender extends JsRender {

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
