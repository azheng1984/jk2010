<?php
namespace Hyperframework\Db;

use InvalidArgumentException;
use LogicException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

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
                throw new InvalidArgumentException(
                    "Argument 'name' is null, shared connection must be named."
                );
            }
            $isShared = $options['is_shared'];
        }
        if ($connection === null) {
            if ($name === null) {
                throw new InvalidArgumentException(
                    "Argument 'name' cannot be null,"
                        . " unless connection is set in argument 'options'."
                );
            }
            if ($isShared === false || isset(self::$pool[$name]) === false) {
                $factory = self::getFactory();
                $connection = $factory->create($name);
                if ($isShared) {
                    self::$pool[$name] = $connection;
                }
            } else {
                $connection = self::$pool[$name];
            }
        } else {
            if ($isShared) {
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

    private static function getFactory() {
        if (self::$factory === null) {
            $class = Config::getString(
                'hyperframework.db.connection.factory_class', ''
            );
            if ($class === '') {
                $class = 'Hyperframework\Db\DbConnectionFactory';
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class of database connection factory"
                            . " '$class' does not exist."
                    );
                }
            }
            self::$factory = new $class;
        }
        return self::$factory;
    }
}
