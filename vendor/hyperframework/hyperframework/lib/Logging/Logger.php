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
        if (self::getThresholdCode() === 5) {
            static::log('DEBUG', $mixed);
        }
    }

    public static function info($mixed) {
        if (self::getThresholdCode() >= 4) {
            static::log('INFO', $mixed);
        }
    }

    public static function notice($mixed) {
        if (self::getThresholdCode() >= 3) {
            static::log('NOTICE', $mixed);
        }
    }

    public static function warn($mixed) {
        if (self::getThresholdCode() >= 2) {
            static::log('WARNING', $mixed);
        }
    }

    public static function error($mixed) {
        if (self::getThresholdCode() >= 1) {
           static::log('ERROR', $mixed);
        }
    }

    public static function fatal($mixed) {
        if (self::getThresholdCode() >= 0) {
            static::log('FATAL', $mixed);
        }
    }

    public static function setLevel($value) {
        if ($value === null) {
            self::$thresholdCode = null;
            return;
        }
        $thresholdCode = LogLevelHelper::getCode($value);
        if ($thresholdCode === null) {
            throw new InvalidArgumentException(
                "Log level '$value' is invalid."
            );
        }
        self::$thresholdCode = $thresholdCode;
    }

    public static function getLevel() {
        return LogLevelHelper::getName(self::$thresholdCode);
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
                        "Log handler class '$class' does not exist, defined in "
                            . "'hyperframework.logging.log_handler_class'."
                    );
                }
                self::$logHandler = new $class;
            }
        }
        return self::$logHandler;
    }

    private static function log($level, $log) {
        Logger::debug(
            ['message' => ['dasfsdf %d'], 'time' => 'x', 'extra' => 'xx']
        );
        Logger::debug('dasfsdf %d', $xx);
        if ($log instanceof Closure) {
            $log = $log();
        }
        if (is_string($log)) {
        }
        if (is_array($log) === false) {
            throw new LoggingException(
                'Log must be an array, ' . gettype($log) . ' given.'
            );
        $log['level'] = $level;
        $logRecord = new LogRecord($log);
        $logHandler = static::getLogHandler();
        $logHandler->handle($logRecord);
    }

    private static function getThresholdCode() {
        if (self::$thresholdCode === null) {
            $level = Config::getString('hyperframework.logging.log_level', '');
            if ($level !== '') {
                $thresholdCode = LogLevelHelper::getCode($level);
                if ($thresholdCode === null) {
                    throw new ConfigException(
                        "Log level '$level' is invalid, defined in "
                            . "'hyperframework.logging.log_level'."
                    );
                }
                self::$thresholdCodel = $thresholdCode;
            } else {
                self::$thresholdCode = 4;
            }
        }
        return self::$thresholdCode;
    }
}
