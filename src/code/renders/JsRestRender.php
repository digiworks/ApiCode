<?php

namespace code\renders;

use code\applications\ApiAppFactory;
use code\configuration\Configurations;
use code\service\ServiceTypes;
use code\utility\Arr;

class JsRestRender extends JsRender {

    private $appVersion = '';

    public function __construct($conf) {
        parent::__construct($conf);
        $this->appVersion = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get(Configurations::VERSION);
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

        $view = [
            'theme' => $this->getThemeInUse(),
            'css' => $this->stylesheets,
            'imports' => $this->imports,
            'view' => $this->addContollerParams() . $clientScript
        ];
        return json_encode($view);
    }

    protected function addContollerParams() {
        $paramStr = parent::addContollerParams();
        $jsString = " baseApp.indexPageApiGateway = '" . $this->controller->getFullUrl() . "';" . $paramStr;
        return $jsString . " ";
    }

    /**
     * 
     * @param string $import
     * @return $this
     */
    public function addImport(string $import) {
        $import['lib'] = $this->controller->getFullUrl() . "/api/file/js/" . $import['lib'] . '?ver=' . $this->appVersion;
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
            $import['lib'] = $this->controller->getFullUrl() . "/api/file/js/" . $import['lib'] . '?ver=' . $this->appVersion;
            $chg_imports[] = $import;
        }
        $this->imports = Arr::mergeRecursive($this->imports, $chg_imports);
        return $this;
    }

}
