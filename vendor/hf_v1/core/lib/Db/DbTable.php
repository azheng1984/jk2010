<?php
namespace Hyperframework\Db;

abstract class DbTable {
    private static $client;
    private $name;

    public function getColumnById($id, $selector) {
        return $this->getClient()->getColumnById(
            $this->getTableName(), $id, $selector
        );
    }

    public function getColumnByColumns($columns, $selector) {
        return $this->getClient()->getColumnByColumns(
            $this->getTableName(), $columns, $selector
        );
    }

    public function getRowById($id, $selector = '*') {
        return $this->getClient()->getRowById(
            $this->getTableName(), $id, $selector
        );
    }

    public function getRowByColumns($columns, $selector = '*') {
        return $this->getClient()->getRowByColumns(
            $this->getTableName(), $columns, $selector = '*'
        );
    }

    public function getAllByColumns($columns, $selector = '*') {
        return $this->getClient()->getAllByColumns(
            $this->getTableName(), $columns, $selector = '*'
        );
    }

    public function insert($row) {
        return $this->getClient()->insert($this->getTableName(), $row);
    }

    public function update($columns, $where/*, ...*/) {
        $args = func_get_args();
        array_unshift($args, $this->getTableName());
        call_user_func_array(array($this->getClient(), 'update'), $args);
    }

    public function updateByColumns($replacementColumns, $filterColumns) {
        return $this->getClient()->updateByColumns(
            $this->getTableName(), $replacementColumns, $filterColumns
        );
    }

    public function save(&$row) {
        return $this->getClient()->save($this->getTableName(), $row);
    }

    public function delete($where/*, ...*/) {
        $args = func_get_args();
        array_unshift($args, $this->getTableName());
        call_user_func_array(array($this->getClient(), 'delete'), $args);
    }

    public function deleteById($id) {
        return $this->getClient()->deleteById($this->getTableName(), $id);
    }

    public function deleteByColumns($columns) {
        $productHandler = DbClient::getHandler('Product');
    }

    protected function getClient() {
        if (self::$client === null) {
            self::$client = new DbClient;
        }
        return self::$client;
    }

    protected function getTableName() {
        if ($this->name === null) {
            $class = get_called_class();
            $position = strrpos($name, '\\');
            if ($position !== false) {
                $class = substr($class, $position + 1);
            }
            if (strncmp('Db', $class, 2) !== 0) {
                throw new Exception;
            }
            $this->name = substr($class, 2);
        }
        return $this->name;
    }
}
