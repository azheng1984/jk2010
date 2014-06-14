<?php
class RequestPath {
    private static $segments;

    public static function initialize($path = null) {
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
