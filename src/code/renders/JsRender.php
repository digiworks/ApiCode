<?php

namespace code\renders;

use code\applications\ApiAppFactory;
use code\components\Component;
use code\controllers\AppController;
use code\exceptions\EngineError;
use code\renders\theme\JsTheme;
use code\renders\theme\JsThemeInterface;
use code\renders\views\View;
use code\utility\Arr;
use code\utility\Curl;
use Slim\Routing\RouteContext;

class JsRender {

    protected $engine;
    protected $imports = [];
    protected $stylesheets = [];

    /** @var bool */
    protected $enableSSRender = true;

    /** @var string */
    protected $entry;

    /** @var string */
    protected $fallback = '';

    /** @var bool */
    protected $debug = false;

    /**
     * 
     * 
     */
    protected $transformer;

    /** @var bool */
    protected $onlyServerTrasnformation = false;

    /** @var View */
    protected $view = null;

    /** @var JsTheme */
    private $themes = null;

    /** @var string */
    private $themeName = null;

    /** @var string */
    private $defualtThemeName = null;

    /** @var SsrLoader */
    private $loader;

    /** AppController */
    protected $controller;

    public function __construct($conf) {
        $this->init($conf);
        $this->loader = new SsrLoader('js/lib/ssr.js');
        $this->loader->setEnableSSRender($this->enableSSRender);
    }

    /**
     * 
     * @param array $conf
     */
    protected function init($conf) {
        $engine = null;
        if (isset($conf['engine']['class'])) {
            $engine = ApiAppFactory::getApp()->newInstance($conf['engine']['class']);
            $this->engine = $engine;
        }

        if (isset($conf['translator'])) {
            $translator = ApiAppFactory::getApp()->newInstance($conf['translator']['class'], [$engine]);
            $this->DOMTransformer($translator);
        }

        if (isset($conf['templates'])) {
            if (isset($conf['templates']['deafult'])) {
                $this->setDefualtThemeName($conf['templates']['deafult']);
            }
            foreach ($conf['templates']['themes'] as $key => $theme) {
                $th = ApiAppFactory::getApp()->newInstance($theme['class'], [$theme['path']]);
                $this->addTheme($th, $key);
            }
        }
        if (isset($conf['stylesheets'])) {
            $this->addStylesheets($conf['stylesheets']);
        }

        if (isset($conf['imports'])) {
            $this->addImports($conf['imports']);
        }
        if (isset($conf['onlyServerTrasnformation'])) {
            $this->setOnlyServerTrasnformation($conf['onlyServerTrasnformation']);
        }
        if (isset($conf['enableSSRender'])) {
            $this->setEnableSSRender($conf['enableSSRender']);
        }
    }

    /**
     * 
     * @param RouteContext $controller
     */
    public function setController(?AppController $controller) {
        $this->controller = $controller;
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getThemeInUse() {
        return is_null($this->themeName) ? $this->defualtThemeName : $this->themeName;
    }

    /**
     * 
     * @param type $themeName
     * @return $this
     */
    public function useTheme($themeName) {
        $this->themeName = $themeName;
        return $this;
    }

    public function getDefualtThemeName(): string {
        return $this->defualtThemeName;
    }

    public function setDefualtThemeName(string $defualtThemeName) {
        $this->defualtThemeName = $defualtThemeName;
        return $this;
    }

    public function addTheme(JsThemeInterface $theme, $name) {
        $this->themes[$name] = $theme;
        return $this;
    }

    /**
     * 
     * @param string $name
     * @return JsThemeInterface
     */
    public function getTheme($name): ?JsThemeInterface {
        $theme = null;
        if (isset($this->themes[$name])) {
            $theme = $this->themes[$name];
        }
        return $theme;
    }

    public function getThemes(): array {
        return $this->themes;
    }

    public function setThemes(array $themes): void {
        $this->themes = $themes;
    }

    public function DOMTransformer($cmd) {
        $this->transformer = $cmd;
        return $this;
    }

    public function getDOMTransformer() {
        return $this->transformer;
    }

    public function getOnlyServerTrasnformation(): bool {
        return $this->onlyServerTrasnformation;
    }

    public function setOnlyServerTrasnformation(bool $onlyServerTrasnformation) {
        $this->onlyServerTrasnformation = $onlyServerTrasnformation;
        return $this;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnableSSRender(bool $enabled = true) {
        $this->enableSSRender = $enabled;
        return $this;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function getEnableSSRender() {
        return $this->enableSSRender;
    }

    /**
     * @param bool $debug
     *
     * @return $this
     */
    public function debug(bool $debug = true) {
        $this->debug = $debug;
        return $this;
    }

    /**
     * 
     * @param string $import
     * @return $this
     */
    public function addImport(string $import) {
        $this->imports[] = $import;
        return $this;
    }

    /**
     * 
     * @param array $imports
     * @return $this
     */
    public function addImports(array $imports) {
        $this->imports = Arr::append($imports, $this->imports);
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getImports(): array {
        return $this->imports;
    }

    /**
     * 
     * @param array $imports
     * @return $this
     */
    public function setImports(array $imports) {
        $this->imports = $imports;
        return $this;
    }

    /**
     * 
     * @param array $stylesheets
     * @return $this
     */
    public function setStylesheets(array $stylesheets) {
        $this->stylesheets = $stylesheets;
        return $this;
    }

    /**
     * 
     * @param array $stylesheets
     * @return $this
     */
    public function addStylesheets(array $stylesheets) {
        $this->stylesheets = Arr::mergeRecursive($stylesheets, $this->stylesheets);
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getStylesheets() {
        return $this->stylesheets;
    }

    /**
     * @param string $fallback
     *
     * @return $this
     */
    public function fallback(string $fallback) {
        $this->fallback = $fallback;
        return $this;
    }

    /**
     * 
     * @return string
     * @throws type
     */
    public function loadImports() {
        if (!$this->enableSSRender) {
            return $this->fallback;
        }

        try {
            $serverScript = $this->importsScript();
            if (!is_null($this->engine)) {
                $result = $this->engine->run($serverScript);
            } else {
                $result = $this->fallback;
            }
        } catch (EngineError $exception) {
            if ($this->debug) {
                throw $exception->getException();
            }
            return $this->fallback;
        }

        $decoded = json_decode($result, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            // Looks like the engine returned a JSON object.
            return $decoded;
        }

        // Looks like the engine returned a string.
        return $result;
    }

    /**
     * 
     * @param type $json
     * @return type
     * @throws type
     */
    public function ssrCompilation($json = false) {
        if (!$this->enableSSRender) {
            return $this->fallback;
        }

        try {
            $this->loadImports();
            if (!is_null($this->getCurrentTheme())) {
                $serverScript = $this->getCurrentTheme()->setView($this->view)->setRenderType(RenderTypes::SERVER)->render();
            } else {
                $serverScript = $this->view->setRenderType(RenderTypes::SERVER)->render();
            }
            $result = $serverScript;
            if (!is_null($this->transformer)) {
                $result = $this->transformer->transform($result);
            }
            if (!is_null($this->engine)) {
                $result = $this->engine->run($result);
            } else {
                $result = $this->fallback;
            }
        } catch (EngineError $exception) {
            if ($this->debug) {
                throw $exception->getException();
            }
            return $this->fallback;
        }

        if ($json) {
            $decoded = json_decode($result, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                // Looks like the engine returned a JSON object.
                return $decoded;
            }
        }
        // Looks like the engine returned a string.
        return $result;
    }

    /**
     * 
     * @param string $view
     * @return string
     */
    public function renderView(string $view): string {
        $this->view = new View($view);
        return $this->render();
    }

    /**
     * 
     * @return string
     */
    public function render() {
        $ssrScript = $this->ssrCompilation();
        if (!is_null($this->getCurrentTheme())) {
            $clientScript = $this->getCurrentTheme()->setView($this->view)->setRenderType(RenderTypes::CLIENT)->render();
        } else {
            $clientScript = $this->view->setRenderType(RenderTypes::CLIENT)->render();
        }
        $scriptLibs = $this->imports;
        $renderer = $this->loader->addStylesheets($this->stylesheets)->setScriptServer($ssrScript);
        if (!is_null($this->transformer)) {
            if ($this->onlyServerTrasnformation) {
                $clientScript = $this->transformer->transform($clientScript);
                $renderer->setLaunchScript($this->transformer->transform($renderer->getLaunchScript()));
            } else {
                $renderer->setClientTypeScript($this->transformer->getTypeString());
            }
            array_unshift($scriptLibs, ['lib' => $this->transformer->getLib(), "tranlsator" => ""]);
        }
        return $renderer->addImports($scriptLibs)->setScriptClient($clientScript)->render();
    }

    /**
     * 
     * @return string
     */
    protected function importsScript(): string {
        $import_scripts = "";
        foreach ($this->imports as $import) {
            $script = Curl::get($import['lib']);
            if (!isset($import['use']) || (isset($import['use']) && $import['use'] == "server")) {
                if (!is_null($this->transformer) && $this->transformer->isType($import['tranlsator'])) {
                    $script = $this->transformer->transform($script);
                }

                $import_scripts .= $script . "\n;";
            }
        }

        return $import_scripts;
    }

    /**
     * 
     * @return type
     */
    public function getCurrentTheme() {
        $theme = null;
        if (!is_null($this->themeName)) {
            $theme = $this->getTheme($this->themeName);
        } else {
            if (!is_null($this->defualtThemeName)) {
                $theme = $this->getTheme($this->defualtThemeName);
            }
        }
        return $theme;
    }

}
