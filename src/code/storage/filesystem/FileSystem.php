<?php

namespace code\storage\filesystem;

use code\applications\ApiAppFactory;
use code\service\ServiceInterface;
use code\service\ServiceTypes;

class FileSystem implements ServiceInterface {

    public function init() {
        
    }

    public function getFile($url) {
        $basePath = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get('env.baseStaticFolderPath', "");
        return new File($basePath . "/" . $url);
    }

}
