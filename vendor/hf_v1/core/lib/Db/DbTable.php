<?php
namespace Hyperframework\Db;

abstract class DbTable {
    public function getColumnById($id, $selector) {
        return DbClient::getColumnById(static::getName(), $id, $selector);
    }

    public function getColumnByColumns($columns, $selector) {
        return DbClient::getColumnByColumns(static::getName(), $columns, $selector);
    }

    public function getRowById($id, $selector = '*') {
        return DbClient::getRowById(static::getName(), $id, $selector);
    }

    public function getRowByColumns($columns, $selector = '*') {
        return DbClient::getRowByColumns(
            static::getName(), $columns, $selector = '*'
        );
    }

    public function getAllByColumns($columns, $selector = '*') {
        return DbClient::getAllByColumns(
            static::getName(), $columns, $selector = '*'
        );
    }

    public function insert($row) {
        return DbClient::insert(static::getTableName(), $row);
    }

    public function save(&$row) {
        DbClient::save(static::getName(), $row);
    }

    public function update($columns, $where/*, ...*/) {
    }

    public function updateByColumns(
        $table, $replacementColumns, $filterColumns
    ) {
    }

    public function delete($where/*, ...*/) {
    }

    public function deleteById($id) {
        return DbClient::deleteById(static::getName(), $id);
    }

    public function deleteByColumns($columns) {
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
