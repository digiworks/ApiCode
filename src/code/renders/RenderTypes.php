<?php

namespace code\renders;

abstract class RenderTypes extends Loader {

    const CLIENT = 1;
    const SERVER = 2;

    private $renderType;

    public function getRenderType() {
        return $this->renderType;
    }

    public function setRenderType($type) {
        $this->renderType = $type;
        return $this;
    }

}
