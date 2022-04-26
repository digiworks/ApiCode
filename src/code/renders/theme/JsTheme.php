<?php

namespace code\renders\theme;

use code\renders\RenderTranslated;
use code\renders\RenderTypes;
use code\renders\views\View;


class JsTheme extends RenderTranslated implements JsThemeInterface {

    protected $bufferedParts;
    protected $buffered;
    protected $template;

    /** @var View */
    protected $view;

    public function __construct($viewFile) {
        parent::__construct($viewFile);
        $this->template = $viewFile;
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
        if (is_null($this->buffered)) {
            $this->buffered = $this->load();
        }
        $script = $this->buffered;
        if ($this->getRenderType() == RenderTypes::SERVER) {
            $script = $this->serverView();
        }
        $scriptsParts = $this->compress($this->view->setRenderType(RenderTypes::CLIENT)->render() . $script);
        return $scriptsParts;
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
