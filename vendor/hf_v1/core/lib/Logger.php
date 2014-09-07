<?php
namespace Hyperframework;

use Exception;
use Closure;

class Logger {
    private static $thresholdCode;
    private static $path;
    private static $levels = array(
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7
    );

    private static function getThresholdCode() {
        if (self::$thresholdCode === null) {
            $level = Config::get('hyperframework.logger.level');
            if ($level !== null && isset(self::$levels[$level])) {
                self::$thresholdCode = self::$levels[$level];
            } else {
                self::$thresholdCode = 6;
            }
        }
        return self::$thresholdCode;
    }

    public static function debug(/*$param, ...*/) {
        if (self::getThresholdCode() === 7) {
            static::write('debug', func_get_args());
        }
    }

    public static function info(/*$param, ...*/) {
        if (self::getThresholdCode() >= 6) {
            static::write('info', func_get_args());
        }
    }

    public static function notice(/*$param, ...*/) {
        if (self::getThresholdCode() >= 5) {
            static::write('notice', func_get_args());
        }
    }

    public static function warn(/*$param, ...*/) {
        if (self::getThresholdCode() >= 4) {
            static::write('warning', func_get_args());
        }
    }

    public static function error(/*$param, ...*/) {
        if (self::getThresholdCode() >= 3) {
            static::write('error', func_get_args());
        }
    }

    public static function critical(/*$param, ...*/) {
        if (self::getThresholdCode() >= 2) {
            static::write('critical', func_get_args());
        }
    }

    public static function alert(/*$param, ...*/) {
        if (self::getThresholdCode() >= 1) {
            static::write('alert', func_get_args());
        }
    }

    public static function emergancy(/*$param, ...*/) {
        static::write('emergancy', func_get_args());
    }

    protected static function getPath() {
        if (self::$path === null) {
            $path = Config::get('hyperframework.logger.path');
            if ($path === null) {
                $path = APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'log'
                    . DIRECTORY_SEPARATOR . 'app.log';
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
        if ($params[0] instanceof Closure) {
            $callback = $params[0];
            $params = $callback();
            if ($params === null) {
                return;
            }
        }
        $writer = Config::get('hyperframework.logger.writer');
        if ($writer !== null) {
            $writer::write($level, $params);
            return;
        }
        $path = static::getPath();
        $content = self::build($level, $params);
        if (file_put_contents($path, $content, FILE_APPEND | LOCK_EX) === false)
        {
            throw new Exception;
        }
    }

    protected static function build($level, $params) {
        $prefix = date('Y/m/d h:i:s') . ' [' . $level . '] ';
        if (is_array($params)) {
            if (count($params) > 1) {
                return $prefix
                    . call_user_func_array('sprintf', $params) . PHP_EOL;
            } else {
                return $prefix . $params[0];
            }
        }
        return $prefix . $params;
    }
}
