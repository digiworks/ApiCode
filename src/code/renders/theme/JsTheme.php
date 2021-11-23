<?php

namespace code\renders\theme;

use code\exceptions\ServerScriptDoesNotExist;
use code\renders\RenderTypes;
use code\renders\View;

class JsTheme extends RenderTypes implements JsThemeInterface
{
    private $bufferedParts;
    private $buffered;
    private $template;
    
    /** @var View */
    private $view;
    

    public function __construct($themeFile) {
        $this->template = $themeFile;
    }
    
    public function setView(View $view) 
    {
        $this->view = $view;
        return $this;
    }
    
    /**
     * 
     * @param string $serverSide
     * @param string $clientSide
     * @return string
     */
    public function render()
    {
        if(is_null($this->bufferedParts))
        {
            $this->bufferedParts = $this->load();
        }
        $scriptsParts = (strlen($this->bufferedParts) ? $this->bufferedParts . "\n;" : "" ) .$this->view->setRenderType(RenderTypes::CLIENT)->render();
        if(is_null($this->buffered))
        {
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
    private function loadTemplate()
    {
        if (!file_exists($this->template)) {
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
