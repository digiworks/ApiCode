<?php

namespace code\renders;

class JsRestRender extends JsRender {

    private $indexPageApiGateway = '';

    public function getIndexPageApiGateway() {
        return $this->indexPageApiGateway;
    }

    public function setIndexPageApiGateway($indexPageApiGateway): void {
        $this->indexPageApiGateway = $indexPageApiGateway;
    }

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
        $jsString = " baseApp.indexPageApiGateway = '" . $this->getIndexPageApiGateway() . "';";
        return $jsString . " ";
    }

}
