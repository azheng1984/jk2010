<?php
namespace Hyperframework\Db;

use PDO;
use Exception;

class DbConnection {
    private static $current;
    private static $identifierQuotationMarks;
    private static $factory;
    private static $pool = array();
    private static $stack = array();

    public static function connect($name = 'default', $options = null) {
        $pdo = null;
        if (isset($options['pdo'])) {
            $pdo = $options['pdo'];
        }
        $isReusable = $name !== null;
        if (isset($options['is_reusable'])) {
            $if ($options['is_reusable'] === true && $name === null) {
                throw new Exception;
            }
            $isReusable = $options['is_reusable'];
        }
        if ($pdo === null) {
            if ($name === null) {
                throw new Exception;
            }
            if ($isReusable === false || isset(self::$pool[$name]) === false) {
                $pdo = self::getFactory()->build($name);
                if ($isReusable) {
                    self::$pool[$name] = $pdo;
                }
            }
        } else {
            if ($isReusable) {
                if (isset(self::$pool[$name]) && $pdo !== self::$pool[$name]) {
                    throw new Exception('confilict');
                }
                self::$pool[$name] = $pdo;
            }
        }
        if (self::$current !== null) {
            self::$identifierQuotationMarks = null;
            self::$stack[] = self::$current;
        }
        self::$current = $pdo;
        return $pdo;
    }

    public static function close() {
        self::$identifierQuotationMarks = null;
        if (count(self::$stack) > 0) {
            self::$current = array_pop(self::$stack);
            return;
        }
        self::$current = null;
    }

    public static function closeAll() {
        self::$stack = array();
        self::$current = null;
    }

    public static function getCurrent() {
        if (self::$current === null) {
            self::connect();
        }
        return self::$current;
    }

    public static function quoteIdentifier($identifier) {
        if (self::$identifierQuotationMarks === null) {
            self::$identifierQuotationMarks =
                static::getIdentifierQuotationMarks();
        }
        return self::$identifierQuotationMarks[0] . $identifier
            . self::$identifierQuotationMarks[1];
    }

    protected static function getFactory() {
        if (self::$factory === null) {
            $class = Config::get('hyperframework.db.connection.factory');
            if ($class !== null) {
                self::$factory = new $class;
            } else {
                self::$factory = new DbConnectionFactory;
            }
        }
        return self::$factory;
    }

    protected static function getIdentifierQuotationMarks() {
        if (self::$current === null) {
            static::connect();
        }
        switch (self::$current->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                return array('`', '`');
            case 'sqlsrv':
                return array('[', ']');
            default:
                return array('"', '"');
        }
    }

    public static function reset() {
        self::$current = null;
        self::$identifierQuotationMarks = null;
        self::$stack = array();
        self::$pool = array();
        self::$factory = null;
    }
}
