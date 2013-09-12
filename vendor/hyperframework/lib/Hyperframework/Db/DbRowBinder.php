<?php
class DbRowBinder {
    const INSERTED = 0;
    const UPDATED = 1;
    const NOT_MODIFIED = 2;

    public static function bind(
        $table, $identityColumns, $replacementColumns = null, &$id = null
    ) {
        $columns = array('id');
        if ($replacementColumns !== null) {
            $columns = array_merge($columns, array_keys($replacementColumns));
        }
        $sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $table .
            ' WHERE ' . implode(' = ? AND ', array_keys($identitiyColumns)) .
            ' = ?';
        $arguments = array_values($identitiyColumns);
        $result = Db::getRow($sql, $arguments);
        $status = self::NOT_MODIFIED;
        if ($result !== false && $replacementColumns !== null) {
            $status = static::updateDifference($table, $result, $replacementColumns);
        }
        if ($result !== false) {
            $id = $result['id'];
            return $status;
        }
        $columns = $identitiyColumns;
        if ($replacementColumns !== null) {
            $columns = $replacementColumns + $identitiyColumns;
        }
        Db::insert($table, $columns);
        if (func_num_args() > 3) {
            $id = Db::getLastInsertId();
        }
        return self::INSERTED;
    }

    private static function updateDifference($table, $from, $to) {
        $columns = array();
        foreach ($to as $key => $value) {
            if ($from[$key] !== $value) {
                $columns[$key] = $value;
            }
        }
        if (count($columns) !== 0) {
            Db::update($table, $columns, 'id = ?', $from['id']);
            return self::UPDATED;
        }
        return self::NOT_MODIFIED;
    }
}
