<?php
namespace Hyperframework\Db;

abstract class DbTable {
    private static $client;

    protected function getClient() {
        if (self::$client === null) {
            self::$client = new DbClient;
        }
        return self::$client;
    }

    public function getColumnById($id, $selector) {
        return $this->getClient()->getColumnById(
            $this->getName(), $id, $selector
        );
        $productDao = DbClient::getDao('Product');
        $productDao->getRowById('');
    }

    public function getColumnByColumns($columns, $selector) {
        return $this->getClient()->getColumnByColumns(
            $this->getName(), $columns, $selector
        );
    }

    public function getRowById($id, $selector = '*') {
        return $this->getClient()->getRowById($this->getName(), $id, $selector);
    }

    public function getRowByColumns($columns, $selector = '*') {
        return $this->getClient()->getRowByColumns(
            $this->getName(), $columns, $selector = '*'
        );
    }

    public function getAllByColumns($columns, $selector = '*') {
        return $this->getClient()->getAllByColumns(
            $this->getName(), $columns, $selector = '*'
        );
    }

    public function insert($row) {
        return $this->getClient()->insert($this->getName(), $row);
    }

    public function update($columns, $where/*, ...*/) {
    }

    public function updateByColumns(
        $table, $replacementColumns, $filterColumns
    ) {
    }

    public function save(&$row) {
        return $this->getClient()->save($this->getName(), $row);
    }

    public function delete($where/*, ...*/) {
    }

    public function deleteById($id) {
        return $this->getClient()->deleteById($this->getName(), $id);
    }

    public function deleteByColumns($columns) {
        $productHandler = DbClient::getHandler('Product');
    }

    protected function getName() {
        $class = get_called_class();
        $position = strrpos($name, '\\');
        if ($position !== false) {
            $class = substr($class, $position + 1);
        }
        if (strncmp('Db', $class, 2) !== 0) {
            throw new Exception;
        }
        return substr($class, 2);
    }
}
