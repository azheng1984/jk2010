<?php
namespace Hyperframework\Db;

use PDO;

class DbConnection {
    private static $current = null;
    private static $pool = array();
    private static $stack = array();
    private static $identifierQuotationMarks;
    private static $factory;

    public static function connect($name = null, $options = null) {
        if (self::$current !== null) {
            self::$stack[] = self::$current;
            self::$identifierQuotationMarks = null;
        }
        $pdo = null;
        if (isset($options['pdo'])) {
            $pdo = $options['pdo'];
        }
        $isReusable = false;
        if (isset($options['is_reusable'])) {
            $isReusable = $options['is_reusable'];
        }
        if ($pdo === null) {
            $pdo = static::create($name, $isReusable);
        } else {
            if ($isReusable) {
            }
        }
        self::$current = $pdo;
        return $pdo;
    }

    public static function close() {
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

    private static function create($name, $isReusable) {
        if ($isReusable && isset(self::$pool[$name])) {
            return self::$pool[$name];
        }
        $pdo = self::getFactory()->get($name);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($isReusable) {
            self::$pool[$name] = $pdo;
        }
        return $pdo;
    }

    private static function getFactory() {
        if (self::$factory === null) {
            self::$factory = new DbConnectionFactory;
        }
        return self::$factory;
    }
}
