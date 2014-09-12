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

    protected static function format($level, array $params) {
        $count = count($params);
        if ($count !== 0 && $params[0] instanceof Closure) {
            if ($count > 1) {
                throw new Exception;
            }
            $callback = $params[0];
            $params = $callback();
            if (is_array($params)) {
                $count = count($params);
            } else {
                throw new Exception;
            }
        }
        if ($count < 2) {
            throw new Exception;
        }
        if (preg_match('/^[A-Z0-9_]+$/', $level) !== 1) {
            throw new Exception;
        }
        $result = self::getTimestamp() . ' | ' . $level;
        $name = null;
        var_dump($params);
        if ($params[0] != '') {
            $name = $params[0];
            if (preg_match('/^[a-zA-Z0-9_.]+$/', $name) === 0
                || $name[0] === '.'
                || substr($name, -1) === '.'
            ) {
                throw new Exception;
            }
            $result .= ' | ' . $name;
        }
        if ($count > 2 && is_array($params[2])) {
            if ($count > 3) {
                throw new Exception;
            }
            if (is_array($params[1])) {
                $params[1] = call_user_func_array('sprintf', $params[1]);
            }
            if ($params[1] != '') {
                if ($name === null) {
                    $result .= ' ||';
                } else {
                    $result .= ' |';
                }
                self::appendValue($result, $params[1]);
            }
            $result .= self::convert($params[2]);
        } else {
            $message = null;
            if ($count > 2 || is_array($params[1])) {
                if (is_array($params[1])) {
                    $params = $params[1];
                } else {
                   unset($params[0]);
                }
                $message = call_user_func_array('sprintf', $params);
            } else {
                $message = $params[1];
            }
            if ($message != '') {
                if ($name === null) {
                    $result .= ' ||';
                } else {
                    $result .= ' |';
                }
                self::appendValue($result, $message);
            }
        }
        return $result . PHP_EOL;
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

    private static function getTimestamp() {
        return date('Y-m-d h:i:s');
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
                $protocol = strtolower($matches[1]);
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

    private static function appendValue(&$data, $value, $prefix = "\t>") {
        if (strpos($value, PHP_EOL) === false) {
            $data .= ' ' . $value;
            return;
        }
        if (strncmp($value, PHP_EOL, strlen(PHP_EOL)) !== 0) {
            $value = ' ' . $value;
        }
        $value = str_replace(PHP_EOL, PHP_EOL . $prefix . ' ', $value);
        $value = str_replace(
            PHP_EOL . $prefix . ' ' . PHP_EOL,
            PHP_EOL . $prefix . PHP_EOL,
            $value
        );
        if (substr($value, -1) === ' ') {
            $tail = substr($value, -strlen($prefix) - strlen(PHP_EOL) - 1);
            if ($tail === PHP_EOL . $prefix . ' ') {
                $value = rtrim($value, ' ');
            }
        }
        $data .= $value;
    }

    private static function convert(array $data, $depth = 1) {
        $result = null;
        $prefix = str_repeat("\t", $depth);
        foreach ($data as $key => $value) {
            if (preg_match('/^[0-9a-zA-Z_]+$/', $key) === 0) {
                throw new Exception;
            }
            $result .= PHP_EOL . $prefix . $key . ':';
            if (is_array($value)) {
                $result .= self::convert($value, $depth + 1);
            } elseif ($value != '') {
                self::appendValue($result, $value, $prefix . "\t>");
            }
        }
        return $result;
    }
}
