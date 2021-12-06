<?php

namespace code\storage\filesystem;

use Closure;
use code\applications\ApiAppFactory;
use code\applications\ApiApplication;
use code\service\ServiceInterface;
use code\service\ServiceTypes;

class FileSystem implements ServiceInterface {

    private $basePath = "";
    private $basePathJS = "";
    private $basePathCss = "";

    /**
     * The application instance.
     *
     * @var ApiApplication
     */
    protected $app;

    /**
     * The array of resolved filesystem drivers.
     *
     * @var array
     */
    protected $disks = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    public function __construct() {
        $this->app = ApiAppFactory::getApp();
    }

    public function init() {
        $this->basePath = $this->app->getService(ServiceTypes::CONFIGURATIONS)->get('env.web.baseStaticFolderPath', "");
        $this->basePathJS = $this->app->getService(ServiceTypes::CONFIGURATIONS)->get('env.web.baseJsFolderPath', "");
        $this->basePathCss = $this->app->getService(ServiceTypes::CONFIGURATIONS)->get('env.web.baseCssFolderPath', "");
    }

    /**
     * 
     * @param type $url
     * @return File
     */
    public function getFile($url) {
        return new File($this->basePath . DIRECTORY_SEPARATOR . $url);
    }

    /**
     * 
     * @param type $url
     * @return File
     */
    public function getJs($url) {
        return new File($this->basePathJS . DIRECTORY_SEPARATOR . $url);
    }

    /**
     * 
     * @param type $url
     * @return File
     */
    public function getCss($url) {
        return new File($this->basePathCss . DIRECTORY_SEPARATOR . $url);
    }

    /**
     * Get a filesystem instance.
     *
     * @param  string|null  $name
     * @return StorageDriver
     */
    public function drive($name = null) {
        return $this->disk($name);
    }

    /**
     * Get a filesystem instance.
     *
     * @param  string|null  $name
     * @return StorageDriver
     */
    public function disk($name = null) {
        $name = $name ?: $this->getDefaultDriver();

        return $this->disks[$name] = $this->get($name);
    }

    /**
     * 
     * @param type $name
     * @return type
     */
    protected function get($name) {
        return $this->disks[$name] ?? $this->resolve($name);
    }

    /**
     * 
     * @param string $name
     * @return type
     */
    protected function resolve($name) {
        if (isset($this->customCreators[$name])) {
            return $this->callCustomCreator();
        }
        $driverMethod = $this->app->newInstance($name);
        return $this->{$driverMethod}();
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->app->getService(ServiceTypes::CONFIGURATIONS)->get('env.filesystems.default', File::class);
    }

    /**
     * Unset the given disk instances.
     *
     * @param  array|string  $disk
     * @return $this
     */
    public function forgetDisk($disk) {
        foreach ((array) $disk as $diskName) {
            unset($this->disks[$diskName]);
        }

        return $this;
    }

    /**
     * Disconnect the given disk and remove from local cache.
     *
     * @param  string|null  $name
     * @return void
     */
    public function purge($name = null) {
        $name = $name ?? $this->getDefaultDriver();

        unset($this->disks[$name]);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback) {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->disk()->$method(...$parameters);
    }

}
