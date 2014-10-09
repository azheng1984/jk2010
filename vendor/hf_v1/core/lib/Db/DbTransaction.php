<?php
namespace Hyperframework\Db;

use Exception;
use PDOException;

class DbTransaction {
    public static function execute($callback) {
        DbClient::beginTransaction();
        try {
            $callback();
            DbClient::commit();
        } catch (Exception $e) {
            DbClient::rollback();
            throw $e;
        }
    }
}
