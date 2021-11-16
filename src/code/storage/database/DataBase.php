<?php
namespace code\storage\database;

use code\applications\ApiAppFactory;
use code\service\ServiceInterface;
use code\service\ServiceTypes;
use code\structure\Structure;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Propel;
use Propel\Runtime\ServiceContainer\ServiceContainerInterface;



class DataBase implements ServiceInterface
{
    
    /**
     *
     * @var Structure 
     */
    
    protected $config;
    
    /**
     *
     * @var ServiceContainerInterface 
     */
    protected $serviceContainer;
    
    /**
     *
     * @var ConnectionInterface 
     */
    protected $connection;

    public function __construct() {
        $this->config = new Structure;
        $this->serviceContainer = Propel::getServiceContainer();
    }

    /**
     * 
     */
    public function init() {
        $this->loadConfiguration();
        //$this->serviceContainer->checkVersion('2.0.0-dev');
        $this->connection = Propel::getServiceContainer()->getConnection();
        return $this;
    }

    /**
     * 
     */
    protected function loadConfiguration() {
        $conf = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->get('propel');
        $this->config->load(isset($conf) ? $conf : []);

        $databases = $this->config->get('database.connections');
        if (isset($databases['default'])) {
            $this->loadDefaultConnection('default', $databases['default'], $this->config->get('paths'));
        }
        $this->serviceContainer->initDatabaseMaps(array (
            'default' => 
                $this->config->get('maps')
          ));
    }

    /**
     * 
     */
    protected function loadDefaultConnection($name, $data, $paths) {
        $this->serviceContainer->setAdapterClass($name, $data['adapter']);
        $manager = new ConnectionManagerSingle();
        $manager->setConfiguration($data + [$paths]);
        $manager->setName($name);
        $this->serviceContainer->setConnectionManager($name, $manager);
        $this->serviceContainer->setDefaultDatasource($name);
    }
    
    

}
