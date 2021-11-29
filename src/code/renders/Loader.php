<?php

namespace code\renders;

use code\applications\ApiAppFactory;
use code\exceptions\ServerScriptDoesNotExist;
use code\service\ServiceTypes;
use code\utility\Curl;

abstract class Loader {

    private $parts = [];

    public function addPart($viewPart) {
        $this->parts[] = $viewPart;
        return $this;
    }

    public function load() {
        $fileSystem = ApiAppFactory::getApp()->getService(ServiceTypes::FILESYSTEM);
        $view = "";
        foreach ($this->parts as $part) {
            if (!$fileSystem->fileExists($part)) {
                throw ServerScriptDoesNotExist::atPath($part);
            }
            $script = Curl::get($part);
            $view .= $script . "\n";
        }
        return $view;
    }

    /**
     * 
     * @param string $script
     * @return string
     */
    protected function compress($script) {
        $script = str_replace("'", "\x27", $script);
        $script = $this->removeComments($script);
        $script = trim(preg_replace('/[\t\n\r\s]+/', ' ', $script)); // tabs,newlines,etc.
        $script = trim(preg_replace('/>(\s)+</m', '><', $script)); // remove spaces
        return $script;
    }

    
    /**
     * 
     * @param string $script
     * @return string
     */
    protected function removeComments($script) {
        $script = preg_replace('#^\s*//.+$#m', "", $script); // remove comments //
        return preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $script); // remove comments /**/
    }

    public abstract function render();
}
