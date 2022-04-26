<?php

namespace code\renders\theme;

use code\renders\RenderTypes;

class JsNullTheme extends JsTheme {

    public function render() {
        $scriptsParts = $this->view->setRenderType(RenderTypes::CLIENT)->render();
        return $scriptsParts;
    }

}
