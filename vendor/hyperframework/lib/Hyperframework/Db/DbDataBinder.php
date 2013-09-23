<?php
namespace Hyperframework\Db;

class DbDataBinder {
    const STATUS_INSERTED = 0;
    const STATUS_UPDATED = 1;
    const STATUS_NOT_MODIFIED = 2;

    const RETURN_STATUS = 1;
    const RETURN_ID = 2;

    public static function bind(
        $table, $filterColumns, $replacementColumns = null, $options = null
    ) {
        list($return, $client, $idKey, $updateFilterKey) =
            static::fetchOptions($options);
        $columns = $idKey !== null &&
            isset($filterColumns[$idKey]) ? array() : array($idKey);
        if ($replacementColumns !== null) {
            $columns = array_merge($columns, array_keys($replacementColumns));
        }
        $sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $table .
            ' WHERE ' . implode(' = ? AND ', array_keys($filterColumns)) .
            ' = ?';
        $arguments = array_values($filterColumns);
        $result = $client::getRow($sql, $arguments);
        if ($result === false) {
            return static::insert(
                $client, $table, $filterColumns, $replacementColumns, $return
            );
        }
        if (isset($filterColumns[$idKey])) {
            $result[$idKey] = $filterColumns[$idKey];
        }
        $status = self::STATUS_NOT_MODIFIED;
        if ($replacementColumns !== null) {
            $status = static::updateDifference(
                $client, $table, $idKey, $result, $replacementColumns
            );
        }
        $id = $result[$idKey]; //todo fix id key = null
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
        $idKey = 'id';
        $updateFilterKey = null;
        if ($options === null) {
            return array($return, $client, $idKey, $idKey);
        }
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'return':
                    $return = $value;
                    break;
                case 'client':
                    $client = $value;
                    break;
                case 'id_key':
                    $idKey = $value;
                    break;
                case 'update_filter_key':
                    $updateFilterKey = $value;
                    break;
           }
        }
        if ($updateFilterKey === null) {
            $updateFilterKey = $idKey;
        }
        return array($return, $client, $idKey, $updateFilterKey);
    }

    private static function insert(
        $client, $table, $filterColumns, $replacementColumns, $return
    ) {
        $columns = $filterColumns;
        if ($replacementColumns !== null) {
            $columns = $replacementColumns + $filterColumns;
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
        $client, $table, $from, $to, $idKey
    ) {
        //TODO set idKey when identiryColumns = string
        $columns = array();
        foreach ($to as $key => $value) {
            if ($from[$key] !== $value) {
                $columns[$key] = $value;
            }
        }
        if (count($columns) !== 0) {
            $client::update(
                $table, $columns, $idKey . ' = ?', $from[$idKey]
            );
            return self::STATUS_UPDATED;
        }
        return self::STATUS_NOT_MODIFIED;
    }
}
