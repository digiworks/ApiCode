<?php

namespace code\components;

use code\applications\ApiAppFactory;
use code\configuration\Configurations;
use code\renders\JsRender;
use code\service\ServicesTrait;
use code\service\ServiceTypes;
use ReflectionClass;

abstract class Component {

    use ServicesTrait;

    private $config_path = "etc/configurations";

    /** string name of component */
    private static $name;

    /** @var JsRender $render */
    private $render;

    public function __construct($name, $conf) {
        $this->addService(ServiceTypes::CONFIGURATIONS, (new Configurations($this->getConfigurationPath()))->init());
        static::setName($name);
        ApiAppFactory::getApp()->setAlias($this->getAliasPath(), $this->getBasePath());
    }

    public function init() {
        $this->loadServices();
    }

    /**
     * 
     * @return string
     */
    public static function getName(): string {
        return static::$name;
    }

    /**
     * 
     * @param string $name
     * @return void
     */
    public static function setName(string $name): void {
        static::$name = $name;
    }

    public function getAliasPath() {
        return static::getName();
    }

    /**
     * 
     * @return array
     */
    protected function defineImports(): array {
        $imports = (array) $this->getService(ServiceTypes::CONFIGURATIONS)->get('imports', []);
        return $imports;
    }

    /**
     * 
     * @return array
     */
    protected function defineStylesheets(): array {
        $styles = (array) $this->getService(ServiceTypes::CONFIGURATIONS)->get('stylesheets', []);
        return $styles;
    }

    protected function defineRoutes(): array {
        $routes = (array) $this->getService(ServiceTypes::CONFIGURATIONS)->get('routes', []);
        return $routes;
    }

    /**
     * 
     */
    public function loadRoutes(): array {
        $routes = [];
        foreach ($this->defineRoutes() as $route) {
            $route['controller'] = $route['controller'];
            $routes[] = $route;
        }
        return $routes;
    }

    /**
     * 
     * @return array
     */
    public function loadImports(): array {
        $imports = [];
        foreach ($this->defineImports() as $import) {
            $import['lib'] = $this->calculatePath($import['lib']);
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
            $stylesheets[] = $this->calculatePath($stylesheet);
        }
        return $stylesheets;
    }

    /**
     * 
     * @return system
     */
    public function getBasePath() {
        $rc = new ReflectionClass(get_class($this));
        return dirname($rc->getFileName());
    }

    /**
     * 
     * @param string $path
     * @return string
     */
    public function calculatePath(string $path): string {
        $fileSystem = $this->getService(ServiceTypes::FILESYSTEM);
        $localPath = $this->getAliasPath() . $path;
        if ($fileSystem->fileExists($localPath)) {
            $path = $localPath;
        }
        return $path;
    }

    /**
     * 
     * @return string
     */
    public function getConfigurationPath(): string {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . $this->config_path;
    }

}
