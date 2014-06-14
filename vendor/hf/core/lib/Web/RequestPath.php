<?php
class RequestPath {
    private static $segments;
    private static $params;

    public static function initialize($segments, $params = array()) {
    }

    public static function getParam($name) {
        if (isset(self::$params[$name])) {
            return self::$params[$name];
        }
    }

    public static function setParam($name, $value) {
        return self::$params[$name] = $value;
    }

    public static function getParams() {
        return self::$params;
    }

    public static function hasParam($name) {
        return isset(self::$params[$name]);
    }

    public static function removeParam($name) {
        unset(self::$params[$name]);
    }

    public static function getSegment($index) {
        if (isset(self::$segments[$index])) {
            return self::$segments[$index];
        }
    }

    public static function rewriteSegment($index, $value) {
        if (isset(self::$segments[$index]) === false) {
            throw new Exception;
        }
        self::$segments[$index] = $value;
    }

    public static function popSegment($value) {
        array_pop(self::$segments);
    }

    public static function pushSegment($value) {
        self::$segments[] = $value;
    }

    public static function getSegments() {
        return self::$segments;
    }

    public static function getDepth() {
        return count(self::$segments);
    }

    public static function reset() {
        self::$segments = null;
    }
}
