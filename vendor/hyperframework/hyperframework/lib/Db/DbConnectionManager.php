<?php
namespace Hyperframework\Db;

use PDO;
use InvalidArgumentException;
use LogicException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

final class DbConnectionManager {
    private static $connection;
//    private static $connectionStack = [];
//   private static $connectionPool = [];
    private static $connectionFactory;

    public static function connect($name = 'default') {
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
        $isPooled = false;
        if ($connection === null) {
            if ($name === null) {
                throw new InvalidArgumentException(
                    "Argument 'name' cannot be null,"
                        . " unless connection is set in argument 'options'."
                );
            }
            if ($isShared === false
                || isset(self::$connectionPool[$name]) === false
            ) {
                $factory = self::getConnectionFactory();
                $connection = $factory->create($name);
            } else {
                $isPooled = true;
                $connection = self::$connectionPool[$name];
            }
        }
        if ($isPooled === false) {
            $type = gettype($connection);
            if ($type !== 'object') {
                if (isset($options['connection'])) {
                    throw new InvalidArgumentException(
                        "The value of option 'connection' must be"
                            . " an object, " . $type . ' given.'
                    );
                } else {
                    throw new LogicException(
                        "Database connection must be an object, $type given."
                    );
                }
            }
            if ($isShared) {
                self::$connectionPool[$name] = $connection;
            }
        }
        if (self::$connection !== null) {
            self::$connectionStack[] = self::$connection;
        }
        self::$connection = $connection;
        return $connection;
    }
    
    public static function setConnection($value) {
    }

    public static function getConnection() {
        if (self::$connection === null) {
            self::connect();
        }
        return self::$connection;
    }

    public static function closeConnection() {
        if (count(self::$connectionStack) > 0) {
            self::$connection = array_pop(self::$connectionStack);
            return;
        }
        self::$connection = null;
    }

    public static function getConnectionFactory() {
        if (self::$connectionFactory === null) {
            $configName = 'hyperframework.db.connection.factory_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                self::$connectionFactory = new DbConnectionFactory;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Database connection factory Class '$class' does not"
                            . " exist, defined in '$configName'."
                    );
                }
                self::$connectionFactory = new $class;
            }
        }
        return self::$connectionFactory;
    }

    public static function setConnectionFactory($value) {
        self::$connectionFactory = $value;
    }

    public static function reset() {
        self::$connection = null;
        self::$connectionStack = [];
        self::$connectionPool = [];
        self::$connectionFactory = null;
    }
}
