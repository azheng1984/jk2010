<?php
namespace Hyperframework\Db;

abstract class DbTable {
    private static $instances = array();
    private static $client;
    private $name;

    public function getColumnById($id, $selector) {
        return static::getClient()->getColumnById(
            static::getTableName(), $id, $selector
        );
    }

    public function getColumnByColumns($columns, $selector) {
        return static::getClient()->getColumnByColumns(
            static::getTableName(), $columns, $selector
        );
    }

    public function getRowById($id, $selector = '*') {
        return static::getClient()->getRowById(
            static::getTableName(), $id, $selector
        );
    }

    public function getRowByColumns($columns, $selector = '*') {
        return static::getClient()->getRowByColumns(
            static::getTableName(), $columns, $selector = '*'
        );
    }

    public function getAllByColumns($columns, $selector = '*') {
        return static::getClient()->getAllByColumns(
            static::getTableName(), $columns, $selector = '*'
        );
    }

    public function insert($row) {
        return static::getClient()->insert(
            static::getTableName(), $row
        );
    }

    public function update($columns, $where/*, ...*/) {
        $args = func_get_args();
        array_unshift($args, static::getTableName());
        return call_user_func_array(
            array(static::getClient(), 'update'), $args
        );
    }

    public function updateByColumns($replacementColumns, $filterColumns) {
        return static::getClient()->updateByColumns(
            static::getTableName(), $replacementColumns, $filterColumns
        );
    }

    public function save(&$row) {
        return static::getClient()->save(
            static::getTableName(), $row
        );
    }

    public function delete($where/*, ...*/) {
        $args = func_get_args();
        array_unshift($args, static::getTableName());
        return call_user_func_array(
            array(static::getClient(), 'delete'), $args
        );
    }

    public function deleteById($id) {
        return static::getClient()->deleteById(static::getTableName(), $id);
    }

    public function deleteByColumns($columns) {
        return static::getClient()->deleteByColumns(
            static::getTableName(), $columns
        );
    }

    protected static function getClient() {
        if (self::$client === null) {
            self::$client = new DbClient;
        }
        return self::$client;
    }

    protected static function getTableName() {
        $instance = self::getInstance();
        if ($instance->name === null) {
            $class = get_called_class();
            $position = strrpos($name, '\\');
            if ($position !== false) {
                $class = substr($class, $position + 1);
            }
            if (strncmp('Db', $class, 2) !== 0) {
                throw new Exception;
            }
            $instance->name = substr($class, 2);
        }
        return $instance->name;
    }

    final protected static function getInstance() {
        $class = get_called_class();
        if (isset(self::$instances[$class]) === false) {
            self::$instances[$class] = new $class;
        }
        return self::$instances[$class];
    }
}
