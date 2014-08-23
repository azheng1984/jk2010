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
        $isShared = $name !== null;
        if (isset($options['is_shared'])) {
            if ($options['is_shared'] === true && $name === null) {
                throw new Exception;
            }
            $isShared = $options['is_shared'];
        }
        if ($connection === null) {
            if ($name === null) {
                throw new Exception;
            }
            if ($isShared === false || isset(self::$pool[$name]) === false) {
                $factory = self::getFactory();
                $connection = $factory::build($name);
                if ($isShared) {
                    self::$pool[$name] = $connection;
                }
            } else {
                $connection = self::$pool[$name];
            }
        } else {
            if ($isShared) {
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

    public static function getConnection($default = 'default') {
        if (self::$current === null && $default !== null) {
            self::connect($default);
        }
        return self::$current;
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

    public static function reset() {
        self::$current = null;
        self::$factory = null;
        self::$stack = array();
        self::$pool = array();
    }

    private static function getFactory() {
        if (self::$factory === null) {
            self::$factory = Config::get(
                'hyperframework.db.connection.factory'
            );
            if (self::$factory === null) {
                self::$factory = '\Hyperframework\Db\DbConnectionFactory';
            }
        }
        return self::$factory;
    }
}
