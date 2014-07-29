<?php
namespace Hyperframework;

class DataFilter {
    public static function run($source, $fields) {
        if (is_string($fields)) {
            return isset($source[$fields]) ? $source[$fields] : null; 
        }
    }
}
