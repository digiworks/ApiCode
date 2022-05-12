<?php

namespace code\renders;

use code\renders\views\RestView;
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

    /**
     * 
     * @param string $view
     * @return string
     */
    public function renderView(string $view): string {

        $this->view = new RestView($this->controller->getRequest(), $view, $this->gateway);
        return $this->render();
    }

    protected function init($conf) {
        if (isset($conf['gateway'])) {
            $this->setGateway($conf['gateway']);
        }
        parent::init($conf);
    }

    /**
     * 
     * @param string $import
     * @return $this
     */
    public function addImport(string $import) {
        $import['lib'] = $this->gateway . "/api/file/js/" . $import['lib'];
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
            $import['lib'] = $this->gateway . "/api/file/js/" . $import['lib'];
            $chg_imports[] = $import;
        }
        $this->imports = Arr::mergeRecursive($this->imports, $chg_imports);
        return $this;
    }

}
