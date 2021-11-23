<?php

namespace code\components;

use code\storage\filesystem\File;

abstract class Component {

    private $id;

    public function getId() {
        return $this->id;
    }

    public function setId($id): void {
        $this->id = $id;
    }

    /**
     * 
     */
    public abstract function loadRoutes();

    /**
     * 
     */
    protected abstract function defineImports(): array;

    /**
     * 
     */
    protected abstract function defineStylesheets(): array;

    /**
     * 
     * @return array
     */
    public function loadImports(): array {
        $imports = [];
        foreach ($this->defineImports() as $import) {
            $import['lib'] = $this->getId() . "/" . $import['lib'];
            $imports[] = $import;
        }
        return $imports;
    }

    /**
     * 
     * @return array
     */
    public function loadStylesheets(): array {
        $stylesheets = [];
        foreach ($this->defineStylesheets() as $stylesheet) {
            $stylesheets[] = $this->getId() . "/" . $stylesheet;
        }
        return $stylesheets;
    }

    /**
     * 
     * @param string $url
     * @return File
     */
    public function getJs($url) {
        return new File($this->getBasePath() . DIRECTORY_SEPARATOR . $url);
    }

    /**
     * 
     * @param string $url
     * @return File
     */
    public function getCss($url) {
        return new File($this->getBasePath() . DIRECTORY_SEPARATOR . $url);
    }

    public function getBasePath() {
        return __DIR__;
    }

}
