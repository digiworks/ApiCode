<?php

namespace code\renders;

class SsrView extends Loader {

    private $buffered;
    private $imports = "";
    private $stylesheets = "";
    private $scriptClient;
    private $scriptServer;

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

    /**
     * 
     * @return string
     */
    public function render() {
        $placeholders = [
            '{{stylesheets}}' => $this->stylesheets,
            '{{imports}}' => $this->imports,
            '{{javascript}}' => $this->scriptClient,
            '{{serverside}}' => $this->scriptServer
        ];
        return strtr($this->buffered,$placeholders);
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

        $import_scripts = "";
        foreach ($imports as $import) {
            $type = "";
            if (!isset($import['use']) || (isset($import['use']) && $import['use'] == "client")) {
                if (!empty($import['tranlsator'])) {
                    $type = 'type="' . $import['tranlsator'] . '"';
                }
                $script = '<script ' . $type . ' src="' . $import['lib'] . '"></script>';
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
            $script = '<link rel="stylesheet" href="' . $stylesheet . '"/>';
            $stylesheet_scripts .= $script . PHP_EOL;
        }

        return $stylesheet_scripts;
    }

}
