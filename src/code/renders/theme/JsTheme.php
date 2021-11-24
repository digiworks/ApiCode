<?php

namespace code\renders\theme;

use code\applications\ApiAppFactory;
use code\exceptions\ServerScriptDoesNotExist;
use code\renders\RenderTranslated;
use code\renders\RenderTypes;
use code\renders\View;
use code\service\ServiceTypes;

class JsTheme extends RenderTranslated implements JsThemeInterface {

    private $bufferedParts;
    private $buffered;
    private $template;
    private $fileSystem;

    /** @var View */
    private $view;

    public function __construct($themeFile) {
        $this->template = $themeFile;
        $this->fileSystem = ApiAppFactory::getApp()->getService(ServiceTypes::FILESYSTEM);
        $this->getTRanslationFiles($this->template);
        $this->addPart($this->template);
    }

    public function setView(View $view) {
        $this->view = $view;
        return $this;
    }

    /**
     * 
     * @param string $serverSide
     * @param string $clientSide
     * @return string
     */
    public function render() {
        if (is_null($this->bufferedParts)) {
            $this->bufferedParts = $this->load();
        }
        $scriptsParts = (strlen($this->bufferedParts) ? $this->bufferedParts . "\n;" : "" ) . $this->view->setRenderType(RenderTypes::CLIENT)->render();
        if (is_null($this->buffered)) {
            $this->buffered = $this->loadTemplate();
        }
        $script = $this->buffered;
        if ($this->getRenderType() == RenderTypes::SERVER) {
            $script = $this->serverView();
        }
        $scriptsParts .= $script;
        return $scriptsParts;
    }

    /**
     * 
     * @throws ServerScriptDoesNotExist
     */
    private function loadTemplate() {
        if (!$this->fileSystem->fileExists($this->template)) {
            throw ServerScriptDoesNotExist::atPath($this->template);
        }
        $script = $this->compress(file_get_contents($this->template));
        return $script;
    }

    /**
     * 
     * @return string
     */
    private function serverView() {
        $command = sprintf(
                "%s;ReactDOMServer.renderToString(React.createElement(App, null));",
                $this->buffered);
        return ($command);
    }

}
