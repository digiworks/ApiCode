<?php

namespace code\logger\monolog;

use code\applications\CoreApplicationInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

class MessageHandler extends AbstractHandler {

    const MSG_SUCCESS = 'success';
    const MSG_INFO = 'info';
    const MSG_WARNING = 'warning';
    const MSG_DANGER = 'danger';

    /**
     * Property app.
     *
     * @var  CoreApplicationInterface
     */
    protected $app;

    /**
     * Property types.
     *
     * @var  array
     */
    protected $types = [
        LogLevel::DEBUG => self::MSG_INFO,
        LogLevel::INFO => self::MSG_INFO,
        LogLevel::NOTICE => self::MSG_WARNING,
        LogLevel::WARNING => self::MSG_WARNING,
        LogLevel::ERROR => self::MSG_DANGER,
        LogLevel::CRITICAL => self::MSG_DANGER,
        LogLevel::ALERT => self::MSG_DANGER,
        LogLevel::EMERGENCY => self::MSG_DANGER,
    ];

    /**
     * Class init.
     *
     * @param CoreApplicationInterface $app    The application.
     * @param bool|int                       $level  The minimum logging level at which this handler will be triggered
     * @param Boolean                        $bubble Whether the messages that are handled can bubble up the stack or
     *                                               not
     */
    public function __construct(CoreApplicationInterface $app, $level = Logger::DEBUG, $bubble = true) {
        parent::__construct($level, $bubble);

        $this->app = $app;
    }

    /**
     * Handles a record.
     *
     * All records may be passed to this method, and the handler should discard
     * those that it does not want to handle.
     *
     * The return value of this function controls the bubbling process of the handler stack.
     * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
     * calling further handlers in the stack with a given log record.
     *
     * @param  array $record The record to handle
     *
     * @return Boolean true means that this handler handled the record, and that bubbling is not permitted.
     *                        false means the record was either not processed or that this handler allows bubbling.
     */
    public function handle(array $record) : bool {
        if (!$this->isHandling($record)) {
            return false;
        }
        $type = $this->types[strtolower($record['level_name'])];
        $this->app->addMessage($record['message'], $type);

        return false === $this->bubble;
    }

}
