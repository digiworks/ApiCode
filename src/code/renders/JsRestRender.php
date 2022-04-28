<?php

namespace code\renders;

class JsRestRender extends JsRender {
    /**
     * 
     * @return string
     */
    public function render() {
        if (!is_null($this->getCurrentTheme())) {
            $clientScript = $this->getCurrentTheme()->setView($this->view)->setRenderType(RenderTypes::CLIENT)->render();
        } else {
            $clientScript = $this->view->setRenderType(RenderTypes::CLIENT)->render();
        }

        return $this->addBaseAppConfig() . $clientScript;
    }

    protected function addBaseAppConfig() {
        $jsString = " baseApp.indexPageApiGateway = '" . $this->controller->getFullUrl() . "';";
        return $jsString . " ";
    }

}
