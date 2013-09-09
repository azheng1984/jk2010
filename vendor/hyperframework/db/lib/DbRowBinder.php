<?php
class DbRowBinder {
    public static function bind(
        $table, $identityColumns, $replacementColumns = null, &$id = null
    ) {
        $columns = array('id');
        if ($replacementColumns !== null) {
            $columns = array_merge($columns, array_keys($replacementColumns));
        }
        $sql = 'SELECT '.implode(', ', $columns).' FROM '.$table.' WHERE '
            .implode(' = ? AND ', array_keys($identitiyColumns)).' = ?';
        $argumentList = array_values($identitiyColumns);
        $result = Db::execute($sql, $argumentList)->fetch(PDO::FETCH_ASSOC);
        if ($result !== false && $replacementColumns !== null) {
            static::updateDifference($table, $result, $replacementColumns);
        }
        if ($result !== false) {
            $id = $result['id'];
            return false;
        }
        $columnList = $identitiyColumns;
        if ($replacementColumns !== null) {
            $columnList = $replacementColumns + $identitiyColumns;
        }
        Db::insert($table, $columnList);
        if (func_num_args() > 3) {
            $id = Db::getLastInsertId();
        }
        return true;
    }

    private static function updateDifference($table, $from, $to) {
        $columnList = array();
        foreach ($to as $key => $value) {
            if ($from[$key] !== $value) {
                $columnList[$key] = $value;
            }
        }
        if (count($columnList) !== 0) {
            Db::update($table, $columnList, 'id = ?', $from['id']);
        }
    }
}
