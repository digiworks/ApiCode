<?php

namespace code\renders;

class View extends RenderTranslated {

    private $buffered;

    public function __construct($viewFile) {
        parent::__construct($viewFile);
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

}
