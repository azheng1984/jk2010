<?php
namespace Hyperframework\Db;

class DbRowBinder {
    const INSERTED = 0;
    const UPDATED = 1;
    const NOT_MODIFIED = 2;

    public static function bind(
        $table,
        $identityColumns,
        $replacementColumns = null,
        $returnType = ,
        $client = '\Hyperframework\Db\DbClient'
    ) {
        $columns = isset($identityColumns['id']) ? array() : array('id');
        if ($replacementColumns !== null) {
            $columns = array_merge($columns, array_keys($replacementColumns));
        }
        $sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $table .
            ' WHERE ' . implode(' = ? AND ', array_keys($identityColumns)) .
            ' = ?';
        $arguments = array_values($identitiyColumns);
        $result = $client::getRow($sql, $arguments);
        if ($result === false) {
            return static::insert(
                $client,
                $table,
                $identityColumns,
                $replacementColumns,
                $returnId
            );
        }
        if ($isset($identityColumns['id'])) {
            $result['id'] = $identityColumns['id'];
        }
        $status = self::NOT_MODIFIED;
        if ($replacementColumns !== null) {
            $status = static::updateDifference(
                $client, $table, $result, $replacementColumns
            );
        }
        return $returnId ? array($status, $result['id']) : $status;
    }

    private static function insert(
        $client, $table, $identityColumns, $replacementColumns, $returnId
    ) {
        $columns = $identitiyColumns;
        if ($replacementColumns !== null) {
            $columns = $replacementColumns + $identitiyColumns;
        }
        $client::insert($table, $columns);
        if ($returnId) {
            return array(self::INSERTED, $client::getLastInsertId());
        }
        return self::INSERTED;
    }

    private static function updateDifference($client, $table, $from, $to) {
        $columns = array();
        foreach ($to as $key => $value) {
            if ($from[$key] !== $value) {
                $columns[$key] = $value;
            }
        }
        if (count($columns) !== 0) {
            $client::update($table, $columns, 'id = ?', $from['id']);
            return self::UPDATED;
        }
        return self::NOT_MODIFIED;
    }
}
