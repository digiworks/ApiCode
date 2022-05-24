<?php

namespace code\renders;

use code\utility\Arr;

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
    
    /**
     * 
     * @param string $import
     * @return $this
     */
    public function addImport(string $import) {
        $import['lib'] = $this->controller->getFullUrl() . "/api/file/js/" . $import['lib'];
        $this->imports[] = $import;
        return $this;
    }

    /**
     * 
     * @param array $imports
     * @return $this
     */
    public function addImports(array $imports) {
        $chg_imports = [];
        foreach ($imports as $import) {
            $import['lib'] = $this->controller->getFullUrl() . "/api/file/js/" . $import['lib'];
            $chg_imports[] = $import;
        }
        $this->imports = Arr::mergeRecursive($this->imports, $chg_imports);
        return $this;
    }

}
