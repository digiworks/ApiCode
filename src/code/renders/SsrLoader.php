<?php

namespace code\renders;

use code\applications\ApiAppFactory;
use code\configuration\Configurations;
use code\service\ServiceTypes;
use code\utility\string\Str;

class SsrLoader extends Loader {

    const launchScriptSSRender = "function init(){ ReactDOM.hydrateRoot(document.getElementById(\"root\"),<App />); } init();";
    const launchScriptClientRender = "function init(){ const root = ReactDOM.createRoot(document.getElementById(\"root\")); root.render(<App />); } init();";

    protected $enableSSRender = true;
    private $buffered;
    private $imports = "";
    private $stylesheets = "";
    private $scriptClient;
    private $clientTypeScript = "text/javascript";
    private $scriptServer;
    private $launchScript;

    public function __construct($ssrFile, $scriptC = null, $scriptS = null, $enableSSRender = true) {
        $this->addPart($ssrFile);
        $this->buffered = $this->load();
        $this->scriptClient = $scriptC;
        $this->scriptServer = $scriptS;
        $this->setEnableSSRender($enableSSRender);
    }

    public function getEnableSSRender() {
        return $this->enableSSRender;
    }

    public function setEnableSSRender($enableSSRender): void {
        $this->enableSSRender = $enableSSRender;
        if ($this->enableSSRender) {
            $this->launchScript = self::launchScriptSSRender;
        } else {
            $this->launchScript = self::launchScriptClientRender;
        }
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

        $env = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get(Configurations::ENV);
        $apiGtw = $env['apiGateway'];
        $version = $env['version'];
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
                    $script = '<script ' . $type . ' src="' . $apiGtw . '/api/file/js/' . $import['lib'] . '?v=' . $version . '"></script>';
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
        $version = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get(Configurations::VERSION);
        $stylesheet_scripts = "";
        foreach ($stylesheets as $stylesheet) {
            if (Str::startsWith($stylesheet, "http", false)) {
                $script = '<link rel="stylesheet" href="' . $stylesheet . '"/>';
            } else {
                $script = '<link rel="stylesheet" href="/api/file/css/' . $stylesheet . '?version=' . $version . '"/>';
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
