<?php
class ActionResult {
    private static $data;

    public static function initialize($data) {
        self::$data = $data;
    }

    public static function get($key = null/*, ...*/) {
        if ($key === null) {
            return self::$data;
        }
        $result = self::$data;
        foreach (func_get_args() as $key) {
            if (isset($result[$key]) === false) {
                return;
            }
            $result = $result[$key];
        }
        return $result;
    }

    public static function reset() {
        self::$data = null;
    }
}
