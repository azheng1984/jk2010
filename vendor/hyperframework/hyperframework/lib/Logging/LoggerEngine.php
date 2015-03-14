<?php
namespace Hyperframework\Logging;

use Closure;
use Hyperframework\Common\Config;
use Hyperframework\Common\ConfigException;
use Hyperframework\Common\ClassNotFoundException;

class LoggerEngine {
    private $level;
    private $logHandler;

    public function log($level, $mixed) {
        if ($level > $this->getLevel()) {
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
        $handler = $this->getLogHandler();
        $handler->handle($logRecord);
    }

    public function setLevel($value) {
        $this->level = $value;
    }

    public function getLevel() {
        if ($this->level === null) {
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
                $this->level = $level;
            } else {
                $this->level = LogLevel::INFO;
            }
        }
        return $this->level;
    }

    public function setLogHandler($logHandler) {
        $this->logHandler = $logHandler;
    }

    public function getLogHandler() {
        if ($this->logHandler === null) {
            $configName = 'hyperframework.logging.log_handler_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $this->logHandler = new LogHandler;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Log handler class '$class' does not exist,"
                            . " set using config '$configName'."
                    );
                }
                $this->logHandler = new $class;
            }
        }
        return $this->logHandler;
    }
}
