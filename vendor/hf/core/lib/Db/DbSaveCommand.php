<?php
namespace Hyperframework\Db;

//$result = DbSaveCommand::execute(array(
//    'table' => 'article',
//    'filters' => array('name' => 'xxx'),
//    'replacements' => array('password', 'xx'),
//    //'should_return_id' => true,
//    //'id_column' => 'id'
//));
//$id = $result['id'];
//
//$result = DbDataBindingCommand::execute(
//    'table' => 'article', 'filters' => array('name' => 'abc')
//);

//$id = $result['id'];
//$status = $result['status'];

//$result = DbDataBindingCommand::execute([
//    'table' => 'article',
//    ...
//]);
//todo
//删除 filter columns，自动识别 id，除非在 options 设置识别域

class DbSaveCommand {
    public static function execute($table, &$row, $options = null) {
        //todo 返回有意义的数据
    }

    protected static function () {
    }

    public static function execute(
        $table, $filterColumns, $replacementColumns = null, $options = null
    ) {
        list($shouldGetLastInsertId, $idName) = static::fetchOptions($options);
        $columns = $idName !== null &&
            isset($filterColumns[$idName]) ? array() : array($idName);
        if ($replacementColumns !== null) {
            $columns = array_merge($columns, array_keys($replacementColumns));
        }
        $sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $table .
            ' WHERE ' . implode(' = ? AND `', array_keys($filterColumns)) .
            '` = ?';
        $arguments = array_values($filterColumns);
        $result = $client::getRow($sql, $arguments);
        if ($result === false) {
            return static::insert(
                $client, $table, $filterColumns, $replacementColumns, $return
            );
        }
        if (isset($filterColumns[$idName])) {
            $result[$idName] = $filterColumns[$idName];
        }
        $status = 'not_modified';//created | updated
        if ($replacementColumns !== null) {
            $status = static::updateDifference(
                $client, $table, $idName, $result, $replacementColumns
            );
        }
        return '';
        $id = $result[$idName]; //todo fix id key = null
        $result = array();
        if (($return & self::RETURN_STATUS) > 0) {
            $result['status'] = $status;
        }
        $result['id'] = $id;
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
        $return = 'status';
        $client = '\Hyperframework\DbClient';
        $idName = 'id';
        if ($options === null) {
            return array($return, $client, $idName);
        }
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'return':
                    $return = $value;
                    break;
                case 'client':
                    $client = $value;
                    break;
                case 'id_name':
                    $idName = $value;
                    break;
            }
        }
        return array($return, $client, $idName);
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
        $client, $table, $from, $to, $idName
    ) {
        //TODO set idName when identityColumns = string
        $columns = array();
        foreach ($to as $key => $value) {
            if ($from[$key] !== $value) {
                $columns[$key] = $value;
            }
        }
        if (count($columns) !== 0) {
            $client::update(
                $table, $columns, $idName . ' = ?', $from[$idName]
            );
            return self::STATUS_UPDATED;
        }
        return self::STATUS_NOT_MODIFIED;
    }
}
