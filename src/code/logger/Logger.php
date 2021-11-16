<?php
namespace code\logger;


use code\logger\LoggerManager;
use code\service\ServiceInterface;



class Logger extends LoggerManager implements ServiceInterface {

    /**
     * Detailed debug information
     */
    const DEBUG = 'debug';

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 'info';

    /**
     * Uncommon events
     */
    const NOTICE = 'notice';

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 'warning';

    /**
     * Runtime errors
     */
    const ERROR = 'error';

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 'critical';

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 'alert';

    /**
     * Urgent alert.
     */
    const EMERGENCY = 'emergency';
    
    /**
     * 
     */
    public function init() {
        $this->logPath = $this->config->get("logs.path");
    }

}
