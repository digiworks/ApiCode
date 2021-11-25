<?php

namespace code\configuration;

use code\service\ServiceInterface;
use code\structure\Structure;

class Configurations implements ServiceInterface, ConfigurationsInterface {

    const ENV = "env";
    const API_GATEWAY_CONFIGURATIONS = "env.apiGateway";

    private static $filepath = '';

    /**
     * All of the items from the config file that is loaded
     *
     * @var Structure
     */
    public static $items;

    public function __construct($filepath) {
        static::$filepath = $filepath;
        static::$items = new Structure();
    }

    /**
     * Loads the config file specified and sets $items to the array
     *
     * @param   string  $filepath
     * @return  void
     */
    public static function load() {
        static::$items->loadFile(static::$filepath . '.php', 'php', ['load_raw' => true]);
    }

    /**
     * Searches the $items array and returns the item
     *
     * @param   string  $item
     * @return  string
     */
    public static function get($key = null, $default = null) {

        if (!empty($key)) {
            return static::$items->get($key, $default);
        }

        return null;
    }

    /**
     * 
     * @param type $path
     * @return type
     */
    public function extract($path) {
        return static::$items->extract($path);
    }

    /**
     * 
     */
    public function init() {
        static::load();
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function createJSEnvinroment(): array {
        $env = $this->get(Configurations::ENV);
        $envJs = [
            'apiGateway' => $env['apiGateway']
        ];

        return $envJs;
    }

}
