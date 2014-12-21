<?php
namespace Hyperframework\Db;

use PDO;
use Exception;
use Hyperframework\Common\Config;

class DbContext {
    private static $current;
    private static $factoryClass;
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
                $factoryClass = self::getFactoryClass();
                $connection = $factoryClass::build($name);
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

    private static function getFactoryClass() {
        if (self::$factoryClass === null) {
            self::$factoryClass = Config::getString(
                'hyperframework.db.connection.factory_class', ''
            );
            if (self::$factoryClass === '') {
                self::$factoryClass = 'Hyperframework\Db\DbConnectionFactory';
            } else {
                if (class_exists(self::$factoryClass) === false) {
                    throw new Exception;
                }
            }
        }
        return self::$factoryClass;
    }
}
