<?php
namespace Hyperframework\Db;

use PDO;
use Exception;
use Hyperframework\Config;

class DbContext {
    private static $current;
    private static $factory;
    private static $stack = array();
    private static $pool = array();

    public static function connect($name = 'default', $options = null) {
        $connection = null;
        if (isset($options['connection'])) {
            $connection = $options['connection'];
        }
        $isReusable = $name !== null;
        if (isset($options['is_reusable'])) {
            if ($options['is_reusable'] === true && $name === null) {
                throw new Exception;
            }
            $isReusable = $options['is_reusable'];
        }
        if ($connection === null) {
            if ($name === null) {
                throw new Exception;
            }
            if ($isReusable === false || isset(self::$pool[$name]) === false) {
                $connection = self::getFactory()->build($name);
                if ($isReusable) {
                    self::$pool[$name] = $connection;
                }
            }
        } else {
            if ($isReusable) {
                if (isset(self::$pool[$name])
                    && $connection !== self::$pool[$name]
                ) {
                    throw new Exception('conflict');
                }
                self::$pool[$name] = $connection;
            }
        }
        if (self::$current !== null) {
            self::$stack[] = self::$current;
        }
        self::$current = $connection;
        return $connection;
    }

    public static function close() {
        if (count(self::$stack) > 0) {
            self::$current = array_pop(self::$stack);
            return;
        }
        self::$current = null;
    }

    public static function closeAll() {
        self::$current = null;
        self::$stack = array();
    }

    public static function getConnection($default = 'default') {
        if (self::$current === null && $default !== null) {
            self::connect($default);
        }
        return self::$current;
    }

    public static function reset() {
        self::$current = null;
        self::$factory = null;
        self::$stack = array();
        self::$pool = array();
    }

    protected static function getFactory() {
        if (self::$factory === null) {
            $class = Config::get('hyperframework.db.connection.factory');
            if ($class !== null) {
                self::$factory =  new $class;
            } else {
                self::$factory = new DbConnectionFactory;
            }
        }
        return self::$factory;
    }
}
