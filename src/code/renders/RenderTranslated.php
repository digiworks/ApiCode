<?php

namespace code\renders;

use code\applications\ApiAppFactory;
use code\service\ServiceTypes;

abstract class RenderTranslated extends RenderTypes {

    protected $fileSystem;

    public function __construct($viewFile) {
        $this->fileSystem = ApiAppFactory::getApp()->getService(ServiceTypes::FILESYSTEM);
    }

    protected function getTRanslationFiles($viewFile) {
        $baseDir = $this->fileSystem->dirname($viewFile);
        $localeFile = $baseDir . DIRECTORY_SEPARATOR . "locale" . DIRECTORY_SEPARATOR . "locale.js";
        if ($this->fileSystem->fileExists($localeFile)) {
            $this->addPart($localeFile);
        }
    }

}
