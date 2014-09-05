<?php
namespace Hyperframework;

use Exception;

class Logger {
    private static $level;
    private static $path;
    private static $levels = array(
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7,
    );

    final protected static function getLevel() {
        if (self::$level === null) {
            $level = Config::get('hyperframework.log_level');
            if ($level !== null && isset(self::$levels[$level])) {
                self::$level = self::$levels[$level];
            } else {
                self::$level = 6;
            }
        }
        return self::$level;
    }

    public static function debug(/*$param, ...*/) {
        if (self::getLevel() === 7) {
            static::write('debug', func_get_args());
        }
    }

    public static function info(/*$param, ...*/) {
        if (self::getLevel() >= 6) {
            static::write('info', func_get_args());
        }
    }

    public static function notice() {
    }

    public static function warn() {
    }

    public static function error() {
    }

    public static function critical() {
    }

    public static function alert() {
    }

    public static function emergancy() {
    }

    protected static function getPath() {
        if (self::$path === null) {
            $path = Config::get('hyperframework.log_path');
            if ($path === null) {
                $path = APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'data'
                    . DIRECTORY_SEPARATOR . 'log.txt';
            } elseif (FullPathRecognizer::isFull($path) === false) {
                $path = APP_ROOT_PATH . DIRECTORY_SEPARATOR . $path;
            }
            self::$path = $path;
        }
        return self::$path;
    }

    protected static function write($level, array $params) {
        $paramCount = count($params);
        if ($paramCount === 0) {
            throw new Exception;
        }
        $writer = Config::get('hyperframework.log_writer');
        if ($writer !== null) {
            $writer::write($level, $params);
            return;
        }
        $message = $params[0];
        $entry = sprintf($message);
        if (file_put_contents(static::getPath(), $entry, FILE_APPEND | LOCK_EX)
            === false
        ) {
            throw new Exception;
        }
    }
}
