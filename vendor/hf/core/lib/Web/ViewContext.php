<?php
class ViewContext {
    private static $stack = array();

    public static function push($data) {
        self::$stack[] = $data;
    }

    public static function pop() {
        array_pop(self::$stack);
    }

    public static function get($name) {
        $current = end($stack);
        if (isset($current[$name])) {
            return $name;
        }
    }
}
