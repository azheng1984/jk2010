<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class Logger {
    /**
     * @param mixed $mixed
     */
    public static function debug($mixed) {
        static::log(LogLevel::DEBUG, $mixed);
    }

    /**
     * @param mixed $mixed
     */
    public static function info($mixed) {
        static::log(LogLevel::INFO, $mixed);
    }

    /**
     * @param mixed $mixed
     */
    public static function notice($mixed) {
        static::log(LogLevel::NOTICE, $mixed);
    }

    /**
     * @param mixed $mixed
     */
    public static function warn($mixed) {
        static::log(LogLevel::WARNING, $mixed);
    }

    /**
     * @param mixed $mixed
     */
    public static function error($mixed) {
        static::log(LogLevel::ERROR, $mixed);
    }

    /**
     * @param mixed $mixed
     */
    public static function fatal($mixed) {
        static::log(LogLevel::FATAL, $mixed);
    }

    /**
     * @param int $level
     * @param mixed $mixed
     */
    public static function log($level, $mixed) {
        static::getEngine()->log($level, $mixed);
    }

    /**
     * @param int $level
     */
    public static function setLevel($level) {
        static::getEngine()->setLevel($level);
    }

    /**
     * @return int
     */
    public static function getLevel() {
        return static::getEngine()->getLevel();
    }

    /**
     * @param ILogHandler $logHandler
     */
    public static function setLogHandler($logHandler) {
        static::getEngine()->setLogHandler($logHandler);
    }

    /**
     * @return ILogHandler
     */
    public static function getLogHandler() {
        return static::getEngine()->getLogHandler();
    }

    /**
     * @param object $logHandler
     */
    public static function setEngine($engine) {
        Registry::set('hyperframework.logging.logger_engine', $engine);
    }

    /**
     * @return object
     */
    public static function getEngine() {
        $engine = Registry::get('hyperframework.logging.logger_engine');
        if ($engine === null) {
            $configName = 'hyperframework.logging.logger_engine_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $engine = new LoggerEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class '$class' does not exist,"
                            . " set using config '$configName'."
                    );
                }
                $engine = new $class;
            }
            static::setEngine($engine);
        }
        return $engine;
    }
}
