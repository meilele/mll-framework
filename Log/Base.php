<?php

namespace Mll\Log;

abstract class Base
{
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';

    public function emergency($message, array $context = array())
    {
        $this->log(self::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->log(self::ALERT, $message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->log(self::ERROR, $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->log(self::WARNING, $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->log(self::NOTICE, $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->log(self::INFO, $message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->log(self::DEBUG, $message, $context);
    }

    abstract public function log($level, $message, array $context = array());
}