<?php
namespace Hyperframework\Db;

use Exception;

class DbTransaction {
    public static function execute($callback) {
        try {
            DbClient::beginTransaction();
            $callback();
            DbClient::commit();
        } catch (Exception $e) {
            DbClient::rollback();
            throw $e;
        }
    }
}
