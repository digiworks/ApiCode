<?php

namespace code\logger\monolog;

use Monolog\Handler\HandlerInterface;

abstract class GlobalContainer {

    /**
     * Property handlers.
     *
     * @var  HandlerInterface[]
     */
    protected static $handlers = [];

    /**
     * Property processors.
     *
     * @var  object[]
     */
    protected static $processors = [];

    /**
     * addHandler
     *
     * @param HandlerInterface $handler
     *
     * @return  void
     */
    public static function addHandler(HandlerInterface $handler) {
        static::$handlers[] = $handler;
    }

    /**
     * Method to get property Handlers
     *
     * @return  HandlerInterface[]
     */
    public static function getHandlers() {
        return static::$handlers;
    }

    /**
     * Method to set property handlers
     *
     * @param   HandlerInterface[] $handlers
     *
     * @return  void
     */
    public static function setHandlers(array $handlers) {
        foreach ($handlers as $handler) {
            static::addHandler($handler);
        }
    }

    /**
     * addProcessor
     *
     * @param object $processor
     *
     * @return  void
     */
    public static function addProcessor($processor) {
        static::$processors[] = $processor;
    }

    /**
     * Method to get property Processors
     *
     * @return  \object[]
     */
    public static function getProcessors() {
        return static::$processors;
    }

    /**
     * Method to set property processors
     *
     * @param   \object[] $processors
     *
     * @return  void
     */
    public static function setProcessors(array $processors) {
        foreach ($processors as $processor) {
            static::addProcessor($processor);
        }
    }

}
