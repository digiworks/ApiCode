<?php
namespace code\applications;


interface CoreApplicationInterface {
    
    public function addMessage($messages, $type = Bootstrap::MSG_INFO); 
}
