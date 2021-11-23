<?php

namespace code\applications;

use code\configuration\Configurations;
use code\service\ServiceTypes;
use code\structure\Structure;
use code\utility\Arr;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Slim\App;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;

class ApiApplication extends App implements CoreApplicationInterface {

    private $config_path = "etc/configurations";
    private $params;
    private $services = [];
    private $middlewares = [];
    private $components = [];

    /**
     * Get the logger.
     *
     * @return  LoggerInterface
     *
     * @since   1.0
     */
    public function getLogger() {
        return $this->getService(ServiceTypes::LOGGER);
    }

    /**
     * 
     * @param ResponseFactoryInterface $responseFactory
     * @param ContainerInterface|null $container
     * @param CallableResolverInterface|null $callableResolver
     * @param RouteCollectorInterface|null $routeCollector
     * @param RouteResolverInterface|null $routeResolver
     * @param MiddlewareDispatcherInterface|null $middlewareDispatcher
     */
    public function __construct(ResponseFactoryInterface $responseFactory, ?ContainerInterface $container = null, ?CallableResolverInterface $callableResolver = null, ?RouteCollectorInterface $routeCollector = null, ?RouteResolverInterface $routeResolver = null, ?MiddlewareDispatcherInterface $middlewareDispatcher = null) {
        parent::__construct($responseFactory, $container, $callableResolver, $routeCollector, $routeResolver, $middlewareDispatcher);
        $this->params = new Structure();
        $this->addService(ServiceTypes::CONFIGURATIONS, (new Configurations($this->config_path))->init());
    }

    public function addService($name, $service) {
        $this->services[$name] = $service;
    }

    public function getService($name) {
        $service = null;
        if (isset($this->services[$name])) {
            $service = $this->services[$name];
        }
        return $service;
    }

    public function addNamedMiddleware($name, $middleware) {
        $this->middlewares[$name] = $middleware;
        $this->add($middleware);
    }

    public function getMiddleware($name) {
        $middleware = null;
        if (isset($this->middlewares[$name])) {
            $middleware = $this->middlewares[$name];
        }
        return $middleware;
    }

    /**
     * 
     * @return $this
     */
    public function init() {
        $this->loadServices();
        $this->loadMiddleware();
        $this->loadRoutes();
        $this->getLogger()->info("info", "Init App");
        return $this;
    }

    /**
     * 
     */
    public function loadServices() {
        $services = (array) $this->getService(ServiceTypes::CONFIGURATIONS)->get('services', []);
        foreach ($services as $name => $class) {
            if (is_callable($class)) {
                $service = $class();
            } else {
                $service = $this->newInstance($class);
            }
            $this->addService($name, $service);
            $service->init();
        }
    }

    /**
     * 
     */
    public function loadMiddleware() {
        $middlewars = (array) $this->getService(ServiceTypes::CONFIGURATIONS)->get('middlewares', []);
        foreach ($middlewars as $name => $middlewareConf) {
            if (is_callable($middlewareConf)) {
                $middleware = $middlewareConf();
            } elseif (is_string($middlewareConf)) {
                $middleware = $this->newInstance($middlewareConf);
            } else {
                if (isset($middlewareConf["options"])) {
                    $middleware = $this->newInstance($middlewareConf["class"], $middlewareConf["options"]);
                } else {
                    $middleware = $this->newInstance($middlewareConf["class"]);
                }
            }
            $this->addNamedMiddleware($name, $middleware);
        }
    }

    /**
     * 
     */
    public function loadRoutes() {
        $routes = (array) $this->getService(ServiceTypes::CONFIGURATIONS)->get('routes', []);
        $this->processRoutes($routes);
    }

    protected function processRoutes(array $routes) {
        foreach ($routes as $route) {
            switch (strtolower($route['method'])) {
                case "get":
                    $this->get($route['route'], $route['controller']);
                    break;
                case "post":
                    $this->post($route['route'], $route['controller']);
                    break;
            }
        }
    }

    public function loadComponents() {
        $components = (array) $this->getService(ServiceTypes::CONFIGURATIONS)->get('components', []);
        foreach ($components as $component) {
            $this->processRoutes($component->loadRoutes());
        }
    }

    /**
     * 
     * @param string $class
     * @param array $args
     * @return object
     * @throws DependencyResolutionException
     * @throws InvalidArgumentException
     */
    public function newInstance($class, array $args = []) {
        if (is_string($class)) {
            try {
                $reflection = new ReflectionClass($class);
            } catch (ReflectionException $e) {
                return false;
            }

            $constructor = $reflection->getConstructor();
            if (null === $constructor) {
                $instance = new $class();
            } else {
                $instance = $reflection->newInstanceArgs($args);
            }
        } elseif (is_callable($class)) {
            $instance = $class($args);
            $reflection = new ReflectionClass($instance);
        } else {
            throw new InvalidArgumentException(
                            'New instance must get first argument as class name, callable.'
            );
        }

        return $instance;
    }

    public function addMessage($messages, $type = Bootstrap::MSG_INFO) {
        
    }

    public function addParams($params) {
        $data = Arr::mergeRecursive($this->params->toArray(), $params);
        $this->params->load($data);
    }

    public function getParams() {
        return $this->params;
    }

}
