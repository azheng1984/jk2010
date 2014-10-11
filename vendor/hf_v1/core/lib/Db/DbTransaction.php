<?php
namespace Hyperframework\Db;

use Exception;

class DbTransaction {
    private static $depth = 0;

    public static function run($callback) {
        if (self::$depth === 0) {
            DbClient::beginTransaction();
        }
        try {
            ++self::$depth;
            $callback();
            DbClient::commit();
        } catch (Exception $e) {
            --self::$depth;
            if (self::$depth === 0) {
                DbClient::rollback();
            }
            throw $e;
        }
        --self::$depth;
    }
}
