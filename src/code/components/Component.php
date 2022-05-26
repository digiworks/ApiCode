<?php

namespace code\components;

use code\applications\ApiAppFactory;
use code\configuration\Configurations;
use code\renders\JsRender;
use code\service\ServiceTypes;
use code\traits\ServicesTrait;
use code\utility\string\StringObject;
use ReflectionClass;

abstract class Component {

    const RENDER_CONFIGURATIONS = "render";

    use ServicesTrait;

    private $config_path = "etc/configurations";

    /** string name of component */
    private static $name;

    /** @var JsRender $render */
    protected $render;

    public function getRender() {
        return $this->render;
    }

    public function __construct($name, $conf) {
        $this->addService(ServiceTypes::CONFIGURATIONS, (new Configurations($this->getConfigurationPath()))->init());
        static::setName($name);
        ApiAppFactory::getApp()->setAlias($this->getAliasPath(), $this->getBasePath());
        $renderManager = ApiAppFactory::getApp()->getService(ServiceTypes::RENDER);
        $this->render = $renderManager->getRender();
        if (isset($conf[static::RENDER_CONFIGURATIONS])) {
            $confRender = $conf[static::RENDER_CONFIGURATIONS];
            if (isset($confRender['class'])) {
                $this->render = ApiAppFactory::getApp()->newInstance($confRender['class'], [$confRender]);
            }
        }
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
        return "@" . static::getName();
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
        if ($this->render->getRemoteRender()) {
            if (!empty($this->render->getGateway())) {
                $strObject = StringObject::create(static::getName());
                ApiAppFactory::getApp()->get("/" . static::getName() . "[/{url}]", 'code\\controllers\\' . $strObject->upperCaseFirst()->getString() . 'Controller:home');
            }
        } else {
            foreach ($this->defineRoutes() as $route) {
                $routes[] = $route;
            }
        }
        return $routes;
    }

    /**
     * 
     * @return array
     */
    public function loadImports(): array {
        $imports = [];
        if (!$this->render->getRemoteRender()) {
            foreach ($this->defineImports() as $import) {
                $import['lib'] = $this->calculatePath($import['lib']);
                $imports[] = $import;
            }
        }
        return $imports;
    }

    /**
     * 
     * @return array
     */
    public function loadStylesheets(): array {
        $stylesheets = [];
        if (!$this->render->getRemoteRender()) {
            foreach ($this->defineStylesheets() as $stylesheet) {
                $stylesheets[] = $this->calculatePath($stylesheet);
            }
        }
        return $stylesheets;
    }

    /**
     * 
     * @return system
     */
    public function getBasePath() {
        $rc = new ReflectionClass(get_class($this));
        $fileSystem = $this->getService(ServiceTypes::FILESYSTEM);
        return $fileSystem->dirname($rc->getFileName());
    }

    /**
     * 
     * @param string $path
     * @return string
     */
    public function calculatePath(string $path): string {
        $fileSystem = $this->getService(ServiceTypes::FILESYSTEM);
        $localPath = $this->getAliasPath() . DIRECTORY_SEPARATOR . $path;
        if ($fileSystem->fileExists(ApiAppFactory::getApp()->getAlias($localPath))) {
            $path = $localPath;
        }
        return $path;
    }

    /**
     * 
     * @return string
     */
    public function getConfigurationPath(): string {
        $fileSystem = $this->getService(ServiceTypes::FILESYSTEM);
        return $fileSystem->createAbsolutePath($this->getBasePath() . DIRECTORY_SEPARATOR . $this->config_path);
    }

}
