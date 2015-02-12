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
        if (self::getThresholdCode() >= LogLevel::DEBUG) {
            static::log('DEBUG', $mixed);
        }
    }

    public static function info($mixed) {
        if (self::getThresholdCode() >= LogLevel::INFO) {
            static::log('INFO', $mixed);
        }
    }

    public static function notice($mixed) {
        if (self::getThresholdCode() >= LogLevel::NOTICE) {
            static::log('NOTICE', $mixed);
        }
    }

    public static function warn($mixed) {
        if (self::getThresholdCode() >= LogLevel::WARNING) {
            static::log('WARNING', $mixed);
        }
    }

    public static function error($mixed) {
        if (self::getThresholdCode() >= LogLevel::ERROR) {
           static::log('ERROR', $mixed);
        }
    }

    public static function fatal($mixed) {
        if (self::getThresholdCode() >= LogLevel::FATAL) {
            static::log('FATAL', $mixed);
        }
    }

    public static function setLevel($value) {
        if ($value === null) {
            self::$thresholdCode = null;
            return;
        }
        $thresholdCode = LogLevel::getCode($value);
        if ($thresholdCode === null) {
            throw new InvalidArgumentException(
                "Log level '$value' is invalid."
            );
        }
        self::$thresholdCode = $thresholdCode;
    }

    public static function getLevel() {
        return LogLevel::getName(self::getThresholdCode());
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

    protected static function log($level, $log) {
        if ($log instanceof Closure) {
            $log = $log();
        }
        if (is_string($log)) {
            $log = ['message' => $log];
        } elseif (is_array($log) === false) {
            throw new LoggingException(
                'Log must be a string or an array, ' . gettype($log) . ' given.'
            );
        }
        $log['level'] = $level;
        $logRecord = new LogRecord($log);
        $logHandler = static::getLogHandler();
        $logHandler->handle($logRecord);
    }

    private static function getThresholdCode() {
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
