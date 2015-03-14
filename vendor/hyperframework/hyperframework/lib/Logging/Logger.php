<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Common\ConfigException;
use Hyperframework\Common\ClassNotFoundException;

class Logger {
    public static function debug($mixed) {
        static::log(LogLevel::DEBUG, $mixed);
    }

    public static function info($mixed) {
        static::log(LogLevel::INFO, $mixed);
    }

    public static function notice($mixed) {
        static::log(LogLevel::NOTICE, $mixed);
    }

    public static function warn($mixed) {
        static::log(LogLevel::WARNING, $mixed);
    }

    public static function error($mixed) {
        static::log(LogLevel::ERROR, $mixed);
    }

    public static function fatal($mixed) {
        static::log(LogLevel::FATAL, $mixed);
    }

    public static function log($level, $mixed) {
        static::getEngine()->log($level, $mixed);
    }

    public static function setLevel($value) {
        static::getEngine()->setLevel($value);
    }

    public static function getLevel() {
        return static::getEngine()->getLevel();
    }

    public static function setEngine($engine) {
        Registry::set('hyperframework.logging.logger_engine', $engine);
    }

    public static function getEngine() {
        $engine = Registry::get('hyperframework.logging.logger_engine');
        if ($engine === null) {
            $class = Config::getString(
                'hyperframework.logging.logger_engine_class', ''
            );
            if ($class === '') {
                $engine = new LoggerEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Log handler class '$class' does not exist,"
                            . " set using config "
                            . "'hyperframework.logging.logger_engine_class'."
                    );
                }
                $engine = new $class;
            }
            static::setEngine($engine);
        }
        return $engine;
    }
}
