<?php

namespace code\components;

abstract class Component {

    private $id;

    public function getId() {
        return $this->id;
    }

    public function setId($id): void {
        $this->id = $id;
    }

    public abstract function loadImports();

    public abstract function loadStylesheets();

    public abstract function loadRoutes();
}
