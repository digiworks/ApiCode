<?php

namespace code\renders;

use code\applications\ApiAppFactory;
use code\configuration\Configurations;
use code\service\ServiceTypes;
use code\utility\string\Str;

class SsrView extends Loader {

    private $buffered;
    private $imports = "";
    private $stylesheets = "";
    private $scriptClient;
    private $clientTypeScript = "text/javascript";
    private $scriptServer;
    private $launchScript = "function init(){ ReactDOM.hydrate(<App />,document.getElementById(\"root\")); } init();";

    public function __construct($ssrFile, $scriptC = null, $scriptS = null) {
        $this->addPart($ssrFile);
        $this->buffered = $this->load();
        $this->scriptClient = $scriptC;
        $this->scriptServer = $scriptS;
    }

    /**
     * 
     * @param string $script
     * @return $this
     */
    public function setScriptClient($script) {
        $this->scriptClient = $script;
        return $this;
    }

    /**
     * 
     * @param string $script
     * @return $this
     */
    public function setScriptServer($script) {
        $this->scriptServer = $script;
        return $this;
    }

    public function setClientTypeScript($clientTypeScript) {
        $this->clientTypeScript = $clientTypeScript;
        return $this;
    }

    public function getClientTypeScript() {
        return $this->clientTypeScript;
    }

    /**
     * 
     * @return string
     */
    public function render() {

        $placeholders = [
            '{{stylesheets}}' => $this->stylesheets,
            '{{imports}}' => $this->imports,
            '{{javascript}}' => $this->scriptClient,
            '{{serverside}}' => $this->scriptServer,
            '{{launchScript}}' => $this->getLaunchScript(),
            '{{typeScript}}' => $this->clientTypeScript,
            '{{envConf}}' => json_encode(ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->createJSEnvinroment())
        ];
        return strtr($this->buffered, $placeholders);
    }

    /**
     * 
     * @param array $imports
     * @return $this
     */
    public function addImports(array $imports) {
        $this->imports = $this->buildClientImports($imports);
        return $this;
    }

    /**
     * 
     * @param array $stylesheets
     * @return $this
     */
    public function addStylesheets(array $stylesheets) {
        $this->stylesheets = $this->buildClientStyleSheets($stylesheets);
        return $this;
    }

    /**
     * 
     * @param array $imports
     * @return string
     */
    private function buildClientImports(array $imports) {

        $apiGtw = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get(Configurations::API_GATEWAY_CONFIGURATIONS);
        $import_scripts = "";
        foreach ($imports as $import) {
            $type = "";
            if (!isset($import['use']) || (isset($import['use']) && $import['use'] == "client")) {
                if (!empty($import['tranlsator'])) {
                    $type = 'type="' . $import['tranlsator'] . '"';
                }
                if (Str::startsWith($import['lib'], "http", false)) {
                    $script = '<script ' . $type . ' src="' . $import['lib'] . '"></script>';
                } else {
                    $script = '<script ' . $type . ' src="' . $apiGtw . '/api/file/js/' . $import['lib'] . '"></script>';
                    //$script = '<script ' . $type . ' src="'. $apiGtw . '/' . $import['lib'] . '"></script>';
                }
                $import_scripts .= $script . PHP_EOL;
            }
        }

        return $import_scripts;
    }

    /**
     * 
     * @param array $stylesheets
     * @return string
     */
    private function buildClientStyleSheets(array $stylesheets) {

        $stylesheet_scripts = "";
        foreach ($stylesheets as $stylesheet) {
            if (Str::startsWith($stylesheet, "http", false)) {
                $script = '<link rel="stylesheet" href="' . $stylesheet . '"/>';
            } else {
                $script = '<link rel="stylesheet" href="/api/file/css/' . $stylesheet . '"/>';
            }
            $stylesheet_scripts .= $script . PHP_EOL;
        }

        return $stylesheet_scripts;
    }

    public function getLaunchScript() {
        return $this->launchScript;
    }

    public function setLaunchScript($launchScript): void {
        $this->launchScript = $launchScript;
    }

}
