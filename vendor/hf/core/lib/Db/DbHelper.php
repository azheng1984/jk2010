<?php

class DbHelper {
    private static function buildWhereByColumns($columns) {
        $params = array();
        $where = null;
        $connection = static::getConnection();
        foreach ($columns as $key => $value) {
            $params[] = $value; 
            if ($where !== null) {
                $where = ' AND ';
            }
            $where .= self::quoteIdentifier($key) . ' = ?';
        }
        if ($where === null) {
            throw new \Exception;
        }
        return array($where, $params);
    }

    private static function getInsertParamPlaceholders($count) {
        if ($count > 1) {
            return str_repeat('?, ', $count - 1) . '?';
        }
        if ($count === 1) {
            return '?';
        }
        return '';
    }
}
