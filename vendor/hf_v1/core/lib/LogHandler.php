<?php
namespace Hyperframework;

use Exception;
use Closure;

class LogHandler {
    private static $protocol;
    private static $path;

    public static function log($level, array $params) {
        $content = static::format($level, $params);
        static::write($content);
    }

    protected static function write($content) {
        $flag = null;
        if (self::getProtocol() === 'file') {
            $flag = FILE_APPEND | LOCK_EX;
        }
        if (file_put_contents(static::getPath(), $content, $flag) === false) {
            throw new Exception;
        }
    }

    protected static function getPath() {
        if (self::$path === null) {
            self::initializePath();
        }
        return self::$path;
    }

    protected static function getProtocol() {
        if (self::$protocol === null) {
            self::initializePath();
        }
        return self::$protocol;
    }

    private static function initializePath() {
        $path = Config::get('hyperframework.log_handler.path');
        if ($path === null) {
            self::$path = APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'log'
                . DIRECTORY_SEPARATOR . 'app.log';
            self::$protocol = 'file';
        } else {
            $protocol = 'file';
            if (preg_match('#^([a-zA-Z0-9.+]+)://#', $path, $matches)) {
                $protocol = $matches[1];
            }
            self::$protocol = $protocol;
            if ($protocol === 'file'
                && FullPathRecognizer::isFull($path) === false
            ) {
                $path = APP_ROOT_PATH . DIRECTORY_SEPARATOR . $path;
            }
            self::$path = $path;
        }
    }

    protected static function format($level, array $params) {
        $count = count($params);
        if ($count > 0 && $params[0] instanceof Closure) {
            if ($count > 1) {
                throw new Exception;
            }
            $callback = $params[0];
            $params = $callback();
            if (is_array($params)) {
                $count = count($params);
            } else {
                $params = array($params);
                $count = 1;
            }
        }
        $prefix = PHP_EOL . date('Y-m-d h:i:s') . ' [' . $level . ']';
        $message = null;
        if ($count > 0) {
            if ($count > 1) {
                $message = call_user_func_array('sprintf', $params);
            } else {
                $message = $params[0];
            }
        }
        if ($message == '') {
            return $prefix;
        }
        if (strncmp($message, PHP_EOL, strlen(PHP_EOL)) !== 0) {
            $prefix = $prefix . ' ';
        }
        return $prefix . str_replace(PHP_EOL, PHP_EOL . "\t", $message);
    }
}
