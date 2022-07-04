<?php

namespace code\storage\database\behaviors;

use Propel\Generator\Model\Behavior;
use Propel\Runtime\Connection\ConnectionInterface;

class FileBehavior extends Behavior {

    protected $tableModificationOrder = 40;
    // default parameters value
    protected $parameters = array(
        'fields' => [],
    );

    public function preSave(ConnectionInterface $con = null) {
        return parent::preSave($con);
    }
}
