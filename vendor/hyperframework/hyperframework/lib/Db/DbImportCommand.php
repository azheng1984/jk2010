<?php
namespace Hyperframework\Db;

use PDO;
use Exception;

class DbImportCommand {
    public static function execute($table, $rows, $options = null) {
        $count = count($rows);
        if ($count === 0) {
            return;
        }
        $columnNames = null;
        if (isset($options['column_names'])) {
            $columnNames = $options['column_names'];
            if (is_array($columnNames) === false) {
                throw new Exception('Options 中的 column_names 值必须是 array. '
                    . gettype($columnNames) . ' given.');
            }
        } else {
            $columnNames = array_keys($rows[0]);
        }
        $columnCount = count($columnNames);
        if ($columnCount === 0) {
            return;
        }
        $batchSize = 1000;
        if ($options['batch_size']) {
            if ($options['batch_size'] === false) {
                $batchSize = $count;
            } else {
                $options['batch_size'] = $options['batch_size'];
            }
        }
        $row = $rows[0];
        foreach ($columnNames as &$columnName) {
            $columnName = DbClient::quoteIdentifier($columnName);
        }
        $prefix = 'INSERT INTO ' . DbClient::quoteIdentifier($table)
            . '(' . implode($columnNames, ', ') . ') VALUES';
        $placeHolders = '(' . str_repeat('?, ', $columnCount - 1) . '?)';
        $statement = null;
        $index = 0;
        while ($index < $count) {
            $values = array();
            $size = $batchSize;
            if ($index + $batchSize >= $count) {
                $size = $count - $index;
            }
            if ($statement === null || $size !== $batchSize) {
                $sql = $prefix . str_repeat($placeHolders . ',', $size - 1)
                    . $placeHolders;
                $statement = DbClient::prepare(
                    $sql, array(PDO::ATTR_EMULATE_PREPARES => false)
                );
            }
            while ($size > 0) {
                if (count($rows[$index]) !== $columnCount) {
                    throw new Exception('导入的数据行，数据列数不一致.');
                }
                $values = array_merge($values, array_values($rows[$index]));
                ++$index;
                --$size;
            }
            $statement->execute($values);
        }
    }
}
