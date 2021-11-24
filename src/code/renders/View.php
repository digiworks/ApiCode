<?php

namespace code\renders;

use code\applications\ApiAppFactory;
use code\service\ServiceTypes;

class View extends RenderTypes {

    private $buffered;
    private $fileSystem;

    public function __construct($viewFile) {
        $this->fileSystem = ApiAppFactory::getApp()->getService(ServiceTypes::FILESYSTEM);
        $this->getTRanslationFiles($viewFile);
        $this->addPart($viewFile);
    }

    public function render() {
        if (is_null($this->buffered)) {
            $this->buffered = $this->load();
        }
        $script = $this->buffered;
        if ($this->getRenderType() == RenderTypes::SERVER) {
            $script = $this->serverView();
        } else {
            $script = $this->clientView();
        }
        return $script;
    }

    /**
     * 
     * @return string
     */
    private function serverView() {
        $command = sprintf(
                "%s;ReactDOMServer.renderToString(React.createElement(IndexPage, null));",
                $this->buffered);
        return ($command);
    }

    /**
     * 
     * @return type
     */
    private function clientView() {
        return ($this->compress($this->buffered));
    }

    protected function getTRanslationFiles($viewFile) {
        $baseDir = $this->fileSystem->dirname($viewFile);
        $localeFile = $baseDir . DIRECTORY_SEPARATOR . "locale" . DIRECTORY_SEPARATOR . "locale.js";
        if ($this->fileSystem->fileExists($localeFile)) {
            $this->addPart($localeFile);
        }
    }

}
