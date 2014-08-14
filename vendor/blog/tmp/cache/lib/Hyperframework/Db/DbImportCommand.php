<?php
namespace Hyperframework\Db;

//使用 ? 拼接 + server side prepared statement
//options
//1. client
//2. batch_size (rows limit on one statement, 默认一次执行，由客户端控制条数)
class DbImportCommand {
    public static function execute(
        $table, $values, $names = null, $options = null
    ) {
    }
}
