<?php
namespace Hyperframework\Db;

use Exception;

class DbTransaction {
    private static $connections = [];
    private static $counts = [];

    public static function run($callback) {
        $connection = DbClient::getConnection();
        $index = array_search($connection, self::$connections, true);
        if ($index === false) {
            self::$connections[] = $connection;
            self::$counts[] = 0;
            end(self::$connections);
            $index = key(self::$connections);
        }
        if (self::$counts[$index] === 0) {
            $connection->beginTransaction();
        }
        try {
            ++self::$counts[$index];
            $callback();
            --self::$counts[$index];
            if (self::$counts[$index] === 0) {
                $connection->commit();
                unset(self::$counts[$index]);
                unset(self::$connections[$index]);
            }
        } catch (Exception $e) {
            --self::$counts[$index];
            if (self::$counts[$index] === 0) {
                $connection->rollback();
                unset(self::$counts[$index]);
                unset(self::$connections[$index]);
            }
            throw $e;
        }
    }
}
