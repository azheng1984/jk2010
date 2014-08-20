<?php
namespace Hyperframework\Db;

//使用 ? 拼接 + server side prepared statement
//options: batch_size (rows limit on one statement, 默认一次执行，由客户端控制条数)
class DbImportCommand {
    public static function execute(
        $table, $values, $names = null, $options = null
    ) {
        //if (isset($options['batch_size']))
    }

    protected static function execute($sql, $values) {
    }
}
