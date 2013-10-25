<?php
namespace Hyperframework\Db;

class Connection {
    private static $current = null;
    private static $pool = array();
    private static $stack = array();
    private static $factory;

    public static function connect(
        $name = 'default', $pdo = null, $isReusable = true,
    ) {
        if (self::$current !== null) {
            self::$stack[] = self::$current;
        }
        if ($pdo !== null) {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);//emulate by default, configurable
        }
        if ($pdo === null) {
            $pdo = self::create($name, $isReusable);
        }
        self::$current = $pdo;
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

    public static function reset() {
        self::$current = null;
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
            self::$factory = new ConnectionFactory;
        }
        return self::$factory;
    }
}
