<?php

namespace code\renders;

use code\applications\ApiAppFactory;
use code\controllers\AppController;
use code\service\ServiceInterface;
use code\service\ServiceTypes;

class RenderManager implements ServiceInterface {

    const RENDER_CONFIGURATIONS = "render";

    /** @var JsRender */
    private $render;

    /**
     * 
     * @return JsRender
     */
    public function getRender(?AppController $controller = null) {
        return $this->render->setController($controller);
    }

    /**
     * 
     */
    public function init() {
        $conf = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get(static::RENDER_CONFIGURATIONS);
        $this->render = ApiAppFactory::getApp()->newInstance($conf['class'], [$conf]);
    }

}
