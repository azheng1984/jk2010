<?php

class DbModel {
    public static function getRowById($id, $selector = '*') {
    }

    public static function save(&$row) {
    }

    public static function insert($row) {
    }

    public static function update($row, $where/*, $mixed, ...*/) {
    }

    public static function delete($where/*, $mixed, ...*/) {
    }

    public static function deleteById($id) {
    }

    protected static function getTableName() {
        return get_called_class();
    }    
}
