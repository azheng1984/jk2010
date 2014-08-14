<?php
namespace Hyperframework\Db;

class DbTransaction {
    public static function execute($callback) {
        try {
            DbClient::beginTransaction();
            $callback();
            DbClient::commit();
        } catch (\Exception $e) {
            DbClient::rollback();
            throw $e;
        }
    }
}
