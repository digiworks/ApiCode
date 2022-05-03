<?php

namespace code\traits;

use code\applications\ApiAppFactory;
use code\applications\CoreApplicationInterface;

trait ServicesTrait {

    private $services = [];

    public function addService($name, $service): void {
        $this->services[$name] = $service;
    }

    public function getService($name) {
        $service = null;
        if (isset($this->services[$name])) {
            $service = $this->services[$name];
        } else {
            if (!($this instanceof CoreApplicationInterface)) {
                $service = ApiAppFactory::getApp()->getService($name);
            }
        }
        return $service;
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
                $service = ApiAppFactory::getApp()->newInstance($class);
            }
            $this->addService($name, $service);
            $service->init();
        }
    }

}
