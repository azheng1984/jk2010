<?php
namespace Hyperframework\Db;

class DbDataBinder {
    const STATUS_INSERTED = 0;
    const STATUS_UPDATED = 1;
    const STATUS_NOT_MODIFIED = 2;

    const RETURN_STATUS = 1;
    const RETURN_ID = 2;

    public static function bind(
        $table, $identityColumns, $replacementColumns = null, $options = null
    ) {
        list($return, $client, $primaryKey) = static::fetchOptions($options);
        $columns = $primaryKey !== null &&
            isset($identityColumns[$primaryKey]) ? array() : array($primaryKey);
        if ($replacementColumns !== null) {
            $columns = array_merge($columns, array_keys($replacementColumns));
        }
        $sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $table .
            ' WHERE ' . implode(' = ? AND ', array_keys($identityColumns)) .
            ' = ?';
        $arguments = array_values($identityColumns);
        $result = $client::getRow($sql, $arguments);
        if ($result === false) {
            return static::insert(
                $client, $table, $identityColumns, $replacementColumns, $return
            );
        }
        if (isset($identityColumns[$primaryKey])) {
            $result[$primaryKey] = $identityColumns[$primaryKey];
        }
        $status = self::STATUS_NOT_MODIFIED;
        if ($replacementColumns !== null) {
            $status = static::updateDifference(
                $client, $table, $primaryKey, $result, $replacementColumns
            );
        }
        $id = $result[$primaryKey]; //todo fix primary key = null
        $result = array();
        if (($return & self::RETURN_STATUS) > 0) {
            $result['status'] = $status;
        }
        if (($return & self::RETURN_ID) > 0) {
            $result['id'] = $id;
        }
        $length = count($result);
        if ($length === 0) {
            return;
        }
        if ($length === 1) {
            return current($result);
        }
        return $result;
    }

    private static function fetchOptions($options) {
        $return = self::RETURN_STATUS;
        $client = '\Hyperframework\Db\DbClient';
        $primaryKey = 'id';
        if ($options === null) {
            return array($return, $client, $primaryKey);
        }
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'return':
                    $return = $value;
                    break;
                case 'client':
                    $client = $value;
                    break;
                case 'primary_key':
                    $primaryKey = $value;
                    break;
            }
        }
        return array($return, $client, $primaryKey);
    }

    private static function insert(
        $client, $table, $identityColumns, $replacementColumns, $return
    ) {
        $columns = $identityColumns;
        if ($replacementColumns !== null) {
            $columns = $replacementColumns + $identityColumns;
        }
        $client::insert($table, $columns);
        $result = array();
        if (($return & self::RETURN_STATUS) > 0) {
            $result['status'] = $status;
        }
        if (($return & self::RETURN_ID) > 0) {
            $result['id'] = $client::getLastInsertId();
        }
        $length = count($result);
        if ($length === 0) {
            return;
        }
        if ($length === 1) {
            return current($result);
        }
        return $result;
   }

    private static function updateDifference(
        $client, $table, $from, $to, $identityColumns
    ) {
        //TODO set primaryKey when identiryColumns = string
        $columns = array();
        foreach ($to as $key => $value) {
            if ($from[$key] !== $value) {
                $columns[$key] = $value;
            }
        }
        if (count($columns) !== 0) {
            $client::update(
                $table, $columns, $primaryKey . ' = ?', $from[$primaryKey]
            );
            return self::STATUS_UPDATED;
        }
        return self::STATUS_NOT_MODIFIED;
    }
}
