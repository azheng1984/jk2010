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
        $returnId = false,
        $client = '\Hyperframework\Db\DbClient'
    ) {
        $columns = isset($identityColumns['id']) ? array() : array('id');
        if ($replacementColumns !== null) {
            $columns = array_merge($columns, array_keys($replacementColumns));
        }
        $sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $table .
            ' WHERE ' . implode(' = ? AND ', array_keys($identitiyColumns)) .
            ' = ?';
        $arguments = array_values($identitiyColumns);
        $result = $client::getRow($sql, $arguments);
        if ($result !== false && $isset($identityColumns['id'])) {
            $result['id'] = $identityColumns['id'];
        }
        $status = self::NOT_MODIFIED;
        if ($result !== false && $replacementColumns !== null) {
            $status = static::updateDifference(
                $client, $table, $result, $replacementColumns
            );
        }
        if ($result !== false) {
            return $returnId ? array($status, $result['id']) : $status;
        }
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
