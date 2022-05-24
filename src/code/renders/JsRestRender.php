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

        $view = [
            'theme' => $this->getThemeInUse(),
            'css' => $this->stylesheets,
            'imports' => $this->imports,
            'view' => $this->addBaseAppConfig() . $clientScript
        ];
        return json_encode($view);
    }

    protected function addBaseAppConfig() {
        $jsString = " baseApp.indexPageApiGateway = '" . $this->controller->getFullUrl() . "';";
        return $jsString . " ";
    }

}
