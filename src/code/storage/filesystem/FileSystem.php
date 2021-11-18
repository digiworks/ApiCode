<?php

namespace code\storage\filesystem;

use code\applications\ApiAppFactory;
use code\service\ServiceInterface;
use code\service\ServiceTypes;

class FileSystem implements ServiceInterface {

    private $basePath = "";
    private $basePathJS = "";
    private $basePathCss = "";

    public function init() {
        $this->basePath = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get('env.web.baseStaticFolderPath', "");
        $this->basePathJS = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get('env.web.baseJsFolderPath', "");
        $this->basePathCss = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get('env.web.baseCssFolderPath', "");
    }

    public function getFile($url) {
        return new File($this->basePath . DIRECTORY_SEPARATOR . $url);
    }

    public function getJs($url) {
        return new File($this->basePathJS . DIRECTORY_SEPARATOR . $url);
    }
    
     public function getCss($url) {
        return new File($this->basePathCss . DIRECTORY_SEPARATOR . $url);
    }

    public function mkdir(string $directory,
            int $permissions = 0777,
            bool $recursive = false) {

        return mkdir($this->basePath . DIRECTORY_SEPARATOR . $directory, $permissions, $recursive);
    }

    public function rmdir(string $directory) {
        return rmdir($this->basePath . DIRECTORY_SEPARATOR . $directory);
    }

    public function diskfreespace($directory) {
        return disk_free_space($directory);
    }

    public function disktotalspace($directory) {
        return disk_total_space($directory);
    }

}
