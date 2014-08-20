<?php
namespace Hyperframework\Db;

//使用 ? 拼接 + server side prepared statement
//options:
//column_names: 设置 rows 的名称, 压缩 rows 的 key
//batch_size (rows limit on one statement, 默认一次执行，由客户端控制条数)
class DbImportCommand {
    public static function execute($table, $rows, $options = null) {
        $keys = array();
        $row = $rows[0];
        foreach (array_keys($row) as $key) {
            $keys[] = DbClient::quoteIdentifier($key);
        }
        $sql = 'INSERT INTO ' . DbClient::quoteIdentifier($table)
            . '(' . implode($keys, ', ') . ') VALUES';
        $count = count($rows);
        for ($index = 0; $index < $count; ++$index) {
            if ($index !== 0) {
                $sql .= ',';
            }
            $sql .= '(' . static::getParamPlaceholders(count($row)) . ')';
        }
        $statement = DbClient::prepare(
            $sql, array(PDO::ATTR_EMULATE_PREPARES => false)
        );
        $statement->execute($rows);
    }

    private static function getInsertParamPlaceholders($count) {
        if ($count > 1) {
            return str_repeat('?, ', $count - 1) . '?';
        }
        if ($count === 1) {
            return '?';
        }
        throw new \Exception;
    }

    protected static function execute($sql, $values) {
    }
}
