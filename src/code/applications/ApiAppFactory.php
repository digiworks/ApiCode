<?php
namespace code\applications;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;


class ApiAppFactory extends AppFactory
{
    private static $app;
    
    public static function getApp()
    {
        return static::$app;
    }


    /**
    * 
    * @param ResponseFactoryInterface|null $responseFactory
    * @param ContainerInterface|null $container
    * @param CallableResolverInterface|null $callableResolver
    * @param RouteCollectorInterface|null $routeCollector
    * @param RouteResolverInterface|null $routeResolver
    * @param MiddlewareDispatcherInterface|null $middlewareDispatcher
    * @return App
    */
    public static function create(
        ?ResponseFactoryInterface $responseFactory = null,
        ?ContainerInterface $container = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null
    ): App {
        static::$responseFactory = $responseFactory ?? static::$responseFactory;
        static::$app = new ApiApplication(
            self::determineResponseFactory(),
            $container ?? static::$container,
            $callableResolver ?? static::$callableResolver,
            $routeCollector ?? static::$routeCollector,
            $routeResolver ?? static::$routeResolver,
            $middlewareDispatcher ?? static::$middlewareDispatcher
        );
        return static::$app; 
    }
}
