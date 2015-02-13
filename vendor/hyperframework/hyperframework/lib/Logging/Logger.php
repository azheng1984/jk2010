<?php
namespace Hyperframework\Logging;

use Closure;
use InvalidArgumentException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ConfigException;
use Hyperframework\Common\ClassNotFoundException;

final class Logger {
    private static $logHandler;
    private static $thresholdCode;

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

    public static function setLevel($value) {
        self::$thresholdCode = $value;
    }

    public static function getLevel() {
        return self::getThresholdCode();
    }

    public static function setLogHandler($value) {
        self::$logHandler = $value;
    }

    public static function getLogHandler() {
        if (self::$logHandler === null) {
            $class = Config::getString(
                'hyperframework.logging.log_handler_class', ''
            );
            if ($class === '') {
                self::$logHandler = new LogHandler;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Log handler class '$class' does not exist,"
                            . " set using config "
                            . "'hyperframework.logging.log_handler_class'."
                    );
                }
                self::$logHandler = new $class;
            }
        }
        return self::$logHandler;
    }

    public static function log($level, $mixed) {
        if ($level > self::getThresholdCode()) {
            return;
        }
        if ($mixed instanceof Closure) {
            $data = $mixed();
        } else {
            $data = $mixed;
        }
        if (is_string($data)) {
            $data = ['message' => $data];
        } elseif (is_array($data) === false) {
            throw new LoggingException(
                'Log must be a string or an array, '
                    . gettype($data) . ' given.'
            );
        }
        $data['level'] = $level;
        $logRecord = new LogRecord($data);
        $logHandler = static::getLogHandler();
        $logHandler->handle($logRecord);
    }

    protected static function getThresholdCode() {
        if (self::$thresholdCode === null) {
            $level = Config::getString('hyperframework.logging.log_level', '');
            if ($level !== '') {
                $thresholdCode = LogLevel::getCode($level);
                if ($thresholdCode === null) {
                    throw new ConfigException(
                        "Log level '$level' is invalid, set using config "
                            . "'hyperframework.logging.log_level'."
                    );
                }
                self::$thresholdCode = $thresholdCode;
            } else {
                self::$thresholdCode = LogLevel::INFO;
            }
        }
        return self::$thresholdCode;
    }
}
