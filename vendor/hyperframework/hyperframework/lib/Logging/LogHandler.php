<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;
use Hyperframework\Common\FileLoader;
use Hyperframework\Common\PathCombiner;
use Hyperframework\Common\FullPathRecognizer;

class LogHandler {
    private static $protocol;
    private static $path;

    public static function handle($level, array $params) {
        $content = static::format($level, $params);
        static::write($content);
    }

    protected static function write($content) {
        $flag = null;
        if (self::getProtocol() === 'file') {
            $flag = FILE_APPEND | LOCK_EX;
        }
        file_put_contents(static::getPath(), $content, $flag);
    }

    protected static function format($level, array $params) {
        $name = isset($params['name']) ? $params['name'] : null;
        $message = isset($params['message']) ? $params['message'] : null;
        $data = isset($params['data']) ? $params['data'] : null;
        $count = count($params);
        $result = self::getTimestamp() . ' | ' . $level;
        if ((string)$name !== '') {
            $result .= ' | ' . $name;
        }
        if ((string)$message !== '') {
            if ((string)$name === '') {
                $result .= ' ||';
            } else {
                $result .= ' |';
            }
            self::appendValue($result, $message);
        }
        if ($data !== null) {
            $result .= self::convert($data);
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
        self::$path = Config::getString(
            'hyperframework.log_handler.log_path', ''
        );
        self::$protocol = 'file';
        if (self::$path === '') {
            self::$path = 'log' . DIRECTORY_SEPARATOR . 'app.log';
        } else {
            if (preg_match('#^([a-zA-Z0-9.+]+)://#', $path, $matches)) {
                self::$protocol = strtolower($matches[1]);
                return;
            }
        }
        self::$path = FileLoader::getFullPath(self::$path);
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
                throw new LoggingException(
                    "Data key '$key' of log entry is invalid."
                );
            }
            $result .= PHP_EOL . $prefix . $key . ':';
            if (is_array($value)) {
                $result .= self::convert($value, $depth + 1);
            } elseif ((string)$value !== '') {
                self::appendValue($result, $value, $prefix . "\t>");
            }
        }
        return $result;
    }
}
