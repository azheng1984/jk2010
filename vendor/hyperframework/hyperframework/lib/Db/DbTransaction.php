<?php
namespace Hyperframework\Db;

use Exception;

class DbTransaction {
    private static $connections = [];
    private static $depth = [];

    public static function run($callback) {
        $connection = DbClient::getConnection();
        $index = array_search($connection, self::$connections, true);
        if ($index === false) {
            self::$connections[] = $connection;
            self::$depth[] = 0;
            end(self::$connections);
            $index = key(self::$connections);
        }
        if (self::$depth[$index] === 0) {
            $connection->beginTransaction();
        }
        try {
            ++self::$depth[$index];
            $callback();
            --self::$depth[$index];
            if (self::$depth[$index] === 0) {
                $connection->commit();
                unset(self::$depth[$index]);
                unset(self::$connections[$index]);
            }
        } catch (Exception $e) {
            --self::$depth[$index];
            if (self::$depth[$index] === 0) {
                $connection->rollback();
                unset(self::$depth[$index]);
                unset(self::$connections[$index]);
            }
            throw $e;
        }
    }
}
