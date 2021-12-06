<?php

namespace code\storage\filesystem;

use Closure;
use code\applications\ApiAppFactory;
use code\service\ServiceInterface;
use code\service\ServiceTypes;
use code\storage\filesystem\mimetypes\FinfoMimeTypeDetector;
use code\storage\filesystem\mimetypes\MimeTypeDetector;

class FileSystem implements ServiceInterface {

    private $basePath = "";
    private $basePathJS = "";
    private $basePathCss = "";

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
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

    /**
     * @var MimeTypeDetector
     */
    private $mimeTypeDetector;

    public function __construct(MimeTypeDetector $mimeTypeDetector = null) {
        $this->app = ApiAppFactory::getApp();
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector();
    }

    public function init() {
        $this->basePath = $this->app->getService(ServiceTypes::CONFIGURATIONS)->get('env.web.baseStaticFolderPath', "");
        $this->basePathJS = $this->app->getService(ServiceTypes::CONFIGURATIONS)->get('env.web.baseJsFolderPath', "");
        $this->basePathCss = $this->app->getService(ServiceTypes::CONFIGURATIONS)->get('env.web.baseCssFolderPath', "");
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

    /**
     * Get a filesystem instance.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function drive($name = null) {
        return $this->disk($name);
    }

    /**
     * Get a filesystem instance.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null) {
        $name = $name ?: $this->getDefaultDriver();

        return $this->disks[$name] = $this->get($name);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->app->getService(ServiceTypes::CONFIGURATIONS)->get('env.filesystems.default', "");
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
