<?php
namespace Hyperframework;

use Exception;
use Closure;

class LogHandler {
    private static $protocol;
    private static $path;
    private static $timestampFormat;

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

    private static function getTimestamp() {
        if (self::$timestampFormat === null) {
            self::$timestampFormat =
                Config::get('hyperframework.log_handler.timestamp_format');
            if (self::$timestampFormat === null) {
                self::$timestampFormat = 'Y-m-d h:i:s';
            }
        }
        return date(self::$timestampFormat);
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
        $result = self::getTimestamp() . ' | ' . $level . ' | ' . $params[0];
        if ($count > 2 && is_array($params[2])) {
            if ($count > 3) {
                throw new Exception;
            }
            if (is_array($params[1])) {
                $params[1] = call_user_func_array('sprintf', $params[1]);
            }
            if ($params[1] != '') {
                $result .= ' |';
                if (strncmp($params[1], PHP_EOL, strlen(PHP_EOL)) !== 0) {
                    $result .= ' ';
                }
                $result .= str_replace(PHP_EOL, PHP_EOL . "\t> ", $params[1]);
            }
            $result .= self::encode($params[2]);
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
                $result .= ' |';
                if (strncmp($message, PHP_EOL, strlen(PHP_EOL)) !== 0) {
                    $result .= ' ';
                }
                $result .= str_replace(PHP_EOL, PHP_EOL . "\t> ", $message);
            }
        }
        return $result . PHP_EOL;
    }

    private static function encode(array $data, $level = 1) {
        $result = null;
        $prefix = str_repeat("\t", $level);
        foreach ($data as $key => $value) {
            $result .= PHP_EOL . $prefix . $key . ':';
            if (is_array($value)) {
                $value = self::encode($value, $level + 1);
            } else {
                $value = str_replace(PHP_EOL, PHP_EOL . $prefix . "\t> ", $value);
                if ($value != '') {
                    $value = ' ' . $value;
                }
            }
            $result .= $value;
        }
        return $result;
    }
}
