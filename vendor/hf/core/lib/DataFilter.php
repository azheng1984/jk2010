<?php
namespace Hyperframework;

class DataFilter {
    public static function run($data, $fields) {
        if (is_string($fields)) {
            return isset($data[$fields]) ? $data[$fields] : null; 
        }
    }
}
