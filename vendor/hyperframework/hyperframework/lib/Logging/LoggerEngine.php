<?php
namespace Hyperframework\Logging;

class LoggerEngine {
    private static $logHandler;
    private static $level;

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
        self::$level = $value;
    }

    public static function getLevel() {
        if (self::$level === null) {
            $name = Config::getString('hyperframework.logging.log_level', '');
            if ($name !== '') {
                $level = LogLevel::getCode($name);
                if ($level === null) {
                    throw new ConfigException(
                        "Log level '$name' is invalid, set using config "
                            . "'hyperframework.logging.log_level'. "
                            . "The available log levels are: "
                            . "DEBUG, INFO, NOTICE, WARNING, ERROR, FATAL, OFF."
                    );
                }
                self::$level = $level;
            } else {
                self::$level = LogLevel::INFO;
            }
        }
        return self::$level;
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
        if ($level > static::getLevel()) {
            return;
        }
        if ($mixed instanceof Closure) {
            $data = $mixed();
        } else {
            $data = $mixed;
        }
        if (is_string($data)) {
            $logRecord = new LogRecord($level, $data);
        } elseif (is_array($data) === false) {
            throw new LoggingException(
                'Log must be a string or an array, '
                    . gettype($data) . ' given.'
            );
        } else {
            $message = isset($data['message']) ? $data['message'] : null;
            $time = isset($data['time']) ? $data['time'] : null;
            $logRecord = new LogRecord($level, $message, $time);
        }
        $logHandler = static::getLogHandler();
        $logHandler->handle($logRecord);
    }
}
