<?php

namespace code\storage\filesystem;

use code\applications\ApiAppFactory;
use code\service\ServiceInterface;
use code\service\ServiceTypes;

class FileSystem implements ServiceInterface {

    private $basePath = "";

    public function init() {
        $this->basePath = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get('env.baseStaticFolderPath', "");
    }

    public function getFile($url) {
        return new File($this->basePath . "/" . $url);
    }

    public function mkdir(string $directory,
            int $permissions = 0777,
            bool $recursive = false) {

        return mkdir($this->basePath . "/" . $directory, $permissions, $recursive);
    }

}
