<?php

namespace code\controllers;

use code\applications\ApiAppFactory;
use code\service\ServiceTypes;

abstract class AppController 
{
    protected $request;
    protected $response;
    
    public function render($currentView, $theme)
    {
        $renderManager = ApiAppFactory::getApp()->getService(ServiceTypes::RENDER);
        $this->response->getBody()->write($renderManager->getRender()->useTheme($theme)->renderView($currentView));
    }
}
